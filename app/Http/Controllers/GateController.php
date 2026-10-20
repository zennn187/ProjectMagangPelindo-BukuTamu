<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGateVisitRequest;
use App\Models\GateLog;
use App\Models\Visit;
use App\Services\PhotoService;
use App\Support\PhoneHelper;
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
    public function store(StoreGateVisitRequest $request, PhotoService $photos): JsonResponse
    {
        $data = $request->validated();

        $photoPath = null;
        if (! empty($data['photo'])) {
            $photoPath = $photos->store($data['photo']);

            if ($photoPath === null) {
                return response()->json([
                    'message' => 'Foto gagal disimpan (maks 2MB, JPG/PNG). Silakan ambil foto kembali lalu coba lagi.',
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
            'identity_number' => $data['identity_number'],
            'plate_number' => null, // dihentikan: gate kini memakai NIK sebagai tanda pengenal
            'person_to_meet' => $data['person_to_meet'],
        ]);

        GateLog::create([
            'visit_id' => $visit->id,
            'direction' => GateLog::DIRECTION_IN,
            'token' => $visit->qr_code_token,
        ]);

        // Send the one-time checkout barcode to the guest's WhatsApp (queued, non-blocking).
        \App\Jobs\SendWhatsAppNotification::dispatch($visit->id, 'checkout-barcode');

        return response()->json([
            'message' => 'Pendaftaran berhasil. Barcode checkout telah dikirim ke WhatsApp Anda ('.PhoneHelper::mask($visit->visitor_phone).'). Tunjukkan barcode tersebut saat keluar.',
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

        // Kartu testing: ambil 1 kunjungan gate terakhir yang punya checkout_token,
        // supaya petugas bisa test scan kamera tanpa buka DB/tinker.
        $latestTestVisit = Visit::whereNotNull('checkout_token')
            ->orderByDesc('id')
            ->first();

        $latestTestQrSvg = $latestTestVisit && $latestTestVisit->checkout_token
            ? \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)
                ->color(9, 26, 58)
                ->generate($latestTestVisit->checkout_token)
            : null;

        return view('gate.scanner', compact('glassQrSvg', 'latestTestVisit', 'latestTestQrSvg'));
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
            'wa_url' => $visit->whatsappUrl($this->whatsappMessage($visit)),
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
            'wa_url' => $visit->whatsappUrl($this->whatsappMessage($visit)),
        ]);
    }

    /**
     * Redirect straight to the visitor's WhatsApp chat (number taken from the
     * gate registration form) with a pre-filled message — no manual typing
     * or picking a contact needed.
     */
    public function chat(Visit $visit)
    {
        Gate::authorize('is-receptionist-or-admin');

        $message = $this->whatsappMessage($visit);

        if (! $visit->visitor_phone) {
            return back()->with('error', "Nomor WhatsApp tamu {$visit->visitor_name} tidak tersedia.");
        }

        return redirect()->away($visit->whatsappUrl($message));
    }

    /**
     * Build the automatic WhatsApp message for a visitor.
     */
    protected function whatsappMessage(Visit $visit): string
    {
        if ($visit->check_out_time) {
            return sprintf(
                "Halo Bapak/Ibu %s, terima kasih telah berkunjung ke PT. Pelindo (Persero) Regional 1 Dumai.\n\n".
                "Kunjungan Anda tercatat:\nMasuk: %s\nKeluar: %s\nDurasi: %s\n\n".
                'Semoga harimu menyenangkan. - Buku Tamu Digital Pelindo',
                $visit->visitor_name,
                $visit->check_in_time?->format('d/m/Y H:i') ?? '-',
                $visit->check_out_time->format('d/m/Y H:i'),
                $visit->durationText() ?? '-'
            );
        }

        return sprintf(
            "Halo Bapak/Ibu %s, kunjungan Anda di PT. Pelindo (Persero) Regional 1 Dumai tercatat masuk pukul %s.\n\n".
            "Barcode checkout Anda (sekali pakai):\n%s\n\n".
            'Tunjukkan barcode tersebut kepada petugas security saat keluar area. Terima kasih.',
            $visit->visitor_name,
            $visit->check_in_time?->format('H:i') ?? '-',
            $visit->checkout_token ? route('gate.pass', $visit->checkout_token) : route('gate.form')
        );
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
            'identity_number' => $visit->identity_number,
            'person_to_meet' => $visit->person_to_meet,
            'check_in' => $visit->check_in_time?->format('H:i'),
            'check_out' => $visit->check_out_time?->format('H:i'),
            'duration' => $visit->durationText(),
            'status' => $visit->status,
        ];
    }
}
