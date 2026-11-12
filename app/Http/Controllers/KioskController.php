<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKioskVisitRequest;
use App\Models\Blacklist;
use App\Models\Employee;
use App\Models\Visit;
use App\Services\PhotoService;
use App\Support\PhoneHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\View\View;

class KioskController extends Controller
{
    /**
     * Public self-service kiosk page (no authentication required).
     */
    public function index(): View
    {
        // Guard: jangan gantung halaman kiosk bila MySQL lambat/lock.
        // Fallback ke daftar kosong agar form tetap bisa dibuka.
        // (PDO MySQL 127.0.0.1 tanpa timeout bisa diam selamanya bila server macet,
        //  jadi baca via koneksi cepat-gagal; gagal = tampilkan form tanpa dropdown.)
        try {
            config(['database.connections.mysql.options' => [\PDO::ATTR_TIMEOUT => 2]]);
            DB::connection()->getPdo();
            $employees = \Illuminate\Support\Facades\Cache::remember('employees.active', 3600, fn () => Employee::active()->orderBy('name')->get());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Kiosk employees load failed: '.get_class($e).': '.$e->getMessage());
            $employees = collect();
        }

        // Nomor WhatsApp tujuan tombol floating (dari .env, bisa format 08xx atau 62xx).
        $waNumber = PhoneHelper::normalize(config('services.whatsapp.reception_number'));

        $waMessage = 'Halo Admin Buku Tamu Pelindo Regional 1 Dumai, saya ingin bertanya seputar kunjungan ke Pelabuhan Dumai.';

        return view('kiosk.index', compact('employees', 'waNumber', 'waMessage'));
    }

    /**
     * Store a new visitor submission with default 'pending' status.
     */
    public function store(StoreKioskVisitRequest $request, PhotoService $photos)
    {
        $data = $request->validated();

        try {

        // If the visitor is delivering something, the employee is optional.
        $employeeId = $data['employee_id'] ?? null;
        $deliveryPref = $data['visit_type'] === Visit::TYPE_DELIVER
            ? ($data['delivery_pref'] ?? null)
            : null;

        $photoData = null;
        if (! empty($data['photo'])) {
            $photoData = $photos->process($data['photo']);

            if ($photoData === null) {
                return response()->json([
                    'message' => 'Foto gagal disimpan (maks 2MB, JPG/PNG). Silakan ambil foto kembali lalu coba lagi.',
                ], 422);
            }
        }

        $visit = DB::transaction(function () use ($data, $employeeId, $deliveryPref, $photoData) {
            $visit = Visit::create([
                'visitor_name' => $data['visitor_name'],
                'visitor_phone' => $data['visitor_phone'],
                'visitor_institution' => $data['visitor_institution'],
                'employee_id' => $employeeId ?? null,
                'purpose' => $data['purpose'],
                'visit_type' => $data['visit_type'],
                'delivery_pref' => $deliveryPref,
                'photo_path' => null,
                'qr_code_token' => Str::random(32),
                'status' => Visit::STATUS_PENDING,
            ]);

            if ($photoData !== null) {
                $visit->photo()->create($photoData);
            }

            return $visit;
        });

        try {
            \App\Jobs\SendWhatsAppNotification::dispatch($visit->id, 'new-visit')->afterResponse();
        } catch (\Throwable $e) {
            // Queue DB mati/penuh jangan bikin submit tamu gagal/lama.
            \Illuminate\Support\Facades\Log::warning('WA dispatch failed: '.$e->getMessage());
        }

        // Blacklist alert: cek di SQL (LIKE dua arah), bukan load semua baris.
        // Portable MySQL + SQLite (SQLite tidak punya CONCAT).
        $visitorName = mb_strtolower(trim($data['visitor_name']));
        $visitorInst = mb_strtolower(trim($data['visitor_institution']));
        $concat = DB::getDriverName() === 'sqlite'
            ? "'%' || LOWER(name_or_institution) || '%'"
            : 'CONCAT("%", LOWER(name_or_institution), "%")';
        $blacklistHit = Blacklist::query()
            ->whereRaw('LOWER(name_or_institution) LIKE ?', ['%'.$visitorName.'%'])
            ->orWhereRaw('? LIKE '.$concat, [$visitorName])
            ->orWhereRaw('? LIKE '.$concat, [$visitorInst])
            ->exists();

        $message = $blacklistHit
            ? 'Kunjungan Anda berhasil dikirim. Silakan lapor ke meja resepsionis untuk proses selanjutnya.'
            : 'Terima kasih, kunjungan Anda berhasil dikirim. Silakan menunggu verifikasi dari resepsionis.';

        return response()->json([
            'visit' => $visit,
            'blacklisted' => (bool) $blacklistHit,
            'message' => $message,
        ]);
        } catch (Throwable $e) {
            $errorId = (string) Str::uuid();
            $message = substr($e->getMessage(), 0, 500);

            foreach (['visitor_name', 'visitor_phone', 'visitor_institution', 'purpose', 'photo'] as $field) {
                $value = $request->input($field);

                if (is_string($value) && $value !== '') {
                    $message = str_replace($value, '[redacted]', $message);
                }
            }

            Log::error('Kiosk submission failed', [
                'error_id' => $errorId,
                'exception_class' => $e::class,
                'message' => $message,
                'file' => basename($e->getFile()),
                'line' => $e->getLine(),
                'vercel_id' => $request->header('x-vercel-id'),
            ]);

            return response()->json([
                'message' => 'Server gagal menyimpan formulir. Catat kode ini dan berikan kepada administrator: '.$errorId,
                'error_id' => $errorId,
            ], 500);
        }
    }

    /**
     * Show the QR badge for an approved visit (used for printing / scanning).
     */
    public function badge(string $token)
    {
        $visit = Visit::where('qr_code_token', $token)->firstOrFail();

        return view('kiosk.badge', compact('visit'));
    }
}
