<?php

namespace App\Http\Controllers;

use App\Models\GateLog;
use App\Models\Visit;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GateController extends Controller
{
    /**
     * Guest registration form — opened when the guest scans the QR
     * code placed on the security post glass.
     */
    public function form(): View
    {
        return view('gate.form');
    }

    /**
     * Store the gate guest registration: create the visit (already "active"),
     * generate the one-time checkout token, log the entry, and send the
     * checkout barcode via WhatsApp.
     */
    public function store(Request $request, WhatsAppService $whatsApp): JsonResponse
    {
        $data = $request->validate([
            'visitor_name' => ['required', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:20'],
            'visitor_institution' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:1000'],
            'person_to_meet' => ['required', 'string', 'max:255'],
            'visitor_phone' => ['required', 'string', 'max:20'],
            'photo' => ['nullable', 'string'], // base64 data URL from webcam
        ]);

        $photoPath = null;
        if (! empty($data['photo'])) {
            $photoPath = $this->storePhoto($data['photo']);

            if ($photoPath === null) {
                return response()->json([
                    'message' => 'Foto gagal disimpan. Silakan ambil foto kembali lalu coba lagi.',
                ], 422);
            }
        }

        $visit = Visit::create([
            'visitor_name' => $data['visitor_name'],
            'visitor_phone' => $data['visitor_phone'],
            'visitor_institution' => $data['visitor_institution'],
            'purpose' => $data['purpose'],
            'photo_path' => $photoPath,
            'qr_code_token' => Str::random(32),
            'checkout_token' => Str::random(32),
            'status' => Visit::STATUS_ACTIVE, // guest is entering through the gate right now
            'check_in_time' => now(),
            'source' => Visit::SOURCE_GATE,
            'identity_number' => $data['identity_number'] ?? null,
            'plate_number' => $data['plate_number'] ?? null,
            'person_to_meet' => $data['person_to_meet'],
        ]);

        GateLog::create([
            'visit_id' => $visit->id,
            'direction' => GateLog::DIRECTION_IN,
            'token' => $visit->qr_code_token,
        ]);

        // Send the one-time checkout barcode to the guest's WhatsApp.
        $whatsApp->notifyVisitorCheckoutBarcode($visit);

        return response()->json([
            'message' => 'Pendaftaran berhasil. Barcode checkout telah dikirim ke WhatsApp Anda ('.$this->maskPhone($visit->visitor_phone).'). Tunjukkan barcode tersebut saat keluar.',
            'check_in_time' => $visit->check_in_time->format('H:i'),
        ]);
    }

    /**
     * Gate pass page: shows the one-time checkout QR code that the guest
     * opens from the WhatsApp link and shows to the security scanner.
     */
    public function pass(string $token): View
    {
        $visit = Visit::where('checkout_token', $token)->firstOrFail();

        return view('gate.pass', compact('visit'));
    }

    /**
     * Barcode scanner page for security staff (reads entry & checkout barcodes).
     */
    public function scanner(): View
    {
        Gate::authorize('is-receptionist-or-admin');

        $glassQrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
            ->color(9, 26, 58)
            ->generate(route('gate.form'));

        return view('gate.scanner', compact('glassQrSvg'));
    }

    /**
     * Process a scanned code (called by the scanner page via AJAX).
     *
     * Supports two barcode types:
     *  - Checkout barcode (checkout_token): marks the visit completed, sets
     *    check_out_time, one-time use only.
     *  - Entry badge barcode (qr_code_token): marks a pending visit as
     *    active (check-in) when the guest walks through the gate.
     */
    public function scan(Request $request): JsonResponse
    {
        Gate::authorize('is-receptionist-or-admin');

        $data = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        // The scanned QR may contain a full URL or a bare token — extract the token.
        $code = trim($data['code']);
        if (preg_match('/([A-Za-z0-9]{32})/', $code, $m)) {
            $code = $m[1];
        }

        // 1) Checkout barcode (one-time use).
        $visit = Visit::where('checkout_token', $code)->first();

        if ($visit) {
            return $this->processCheckout($visit, $code);
        }

        // 2) Entry badge barcode (check-in at the gate).
        $visit = Visit::where('qr_code_token', $code)->first();

        if ($visit) {
            return $this->processEntry($visit, $code);
        }

        return response()->json([
            'status' => 'error',
            'title' => 'Barcode tidak dikenal',
            'message' => 'Barcode tidak terdaftar dalam sistem. Silakan periksa kembali atau daftarkan tamu melalui form gate.',
        ], 404);
    }

    /**
     * Mark a visit as completed via its one-time checkout barcode.
     */
    protected function processCheckout(Visit $visit, string $code): JsonResponse
    {
        if ($visit->checkout_used_at !== null) {
            return response()->json([
                'status' => 'error',
                'title' => 'Barcode sudah dipakai',
                'message' => "Barcode checkout atas nama {$visit->visitor_name} sudah digunakan pada {$visit->checkout_used_at->format('H:i')}. Barcode hanya berlaku satu kali.",
            ], 422);
        }

        $visit->update([
            'status' => Visit::STATUS_COMPLETED,
            'check_out_time' => now(),
            'checkout_used_at' => now(),
        ]);

        GateLog::create([
            'visit_id' => $visit->id,
            'direction' => GateLog::DIRECTION_OUT,
            'token' => $code,
        ]);

        return response()->json([
            'status' => 'success',
            'title' => 'Checkout berhasil',
            'message' => sprintf(
                'Tamu atas nama %s sudah melakukan checkout pada jam %s. Durasi kunjungan: %s.',
                $visit->visitor_name,
                $visit->check_out_time->format('H:i'),
                $visit->durationText() ?? '-'
            ),
            'event' => $this->visitEvent($visit->fresh()),
        ]);
    }

    /**
     * Check a pending/waiting visit in at the gate via its badge barcode.
     */
    protected function processEntry(Visit $visit, string $code): JsonResponse
    {
        if ($visit->status === Visit::STATUS_ACTIVE) {
            return response()->json([
                'status' => 'warning',
                'title' => 'Sudah check-in',
                'message' => "Tamu {$visit->visitor_name} sudah berada di dalam area sejak {$visit->check_in_time?->format('H:i')}.",
            ], 422);
        }

        if (! in_array($visit->status, [Visit::STATUS_PENDING, Visit::STATUS_WAITING], true)) {
            return response()->json([
                'status' => 'error',
                'title' => 'Tidak dapat check-in',
                'message' => "Kunjungan atas nama {$visit->visitor_name} berstatus '{$visit->status}' dan tidak dapat di-check-in.",
            ], 422);
        }

        $visit->update([
            'status' => Visit::STATUS_ACTIVE,
            'check_in_time' => now(),
        ]);

        GateLog::create([
            'visit_id' => $visit->id,
            'direction' => GateLog::DIRECTION_IN,
            'token' => $code,
        ]);

        return response()->json([
            'status' => 'success',
            'title' => 'Check-in berhasil',
            'message' => "Selamat datang, {$visit->visitor_name}. Check-in tercatat pukul {$visit->check_in_time->format('H:i')}.",
            'event' => $this->visitEvent($visit->fresh()),
        ]);
    }

    /**
     * Serialise a visit for the scanner / report realtime payloads.
     */
    protected function visitEvent(Visit $visit): array
    {
        return [
            'id' => $visit->id,
            'name' => $visit->visitor_name,
            'institution' => $visit->visitor_institution,
            'plate_number' => $visit->plate_number,
            'person_to_meet' => $visit->person_to_meet,
            'check_in' => $visit->check_in_time?->format('H:i'),
            'check_out' => $visit->check_out_time?->format('H:i'),
            'duration' => $visit->durationText(),
            'status' => $visit->status,
        ];
    }

    /**
     * Mask phone number for confirmation messages: 0812xxx678.
     */
    protected function maskPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) <= 6) {
            return $phone;
        }

        return substr($phone, 0, 4).str_repeat('x', strlen($phone) - 7).substr($phone, -3);
    }

    /**
     * Decode the base64 data-URL photo and persist it under the public disk.
     */
    protected function storePhoto(string $dataUrl): ?string
    {
        $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $dataUrl);

        if (! $base64 || ! Str::startsWith($dataUrl, 'data:image/')) {
            return null;
        }

        $image = base64_decode($base64, true);

        if ($image === false) {
            return null;
        }

        $filename = 'visits/'.Str::uuid().'.jpg';

        if (! \Storage::disk('public')->put($filename, $image)) {
            return null;
        }

        return $filename;
    }
}
