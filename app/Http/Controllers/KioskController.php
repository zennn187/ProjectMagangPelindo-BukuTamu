<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use App\Models\Employee;
use App\Models\Visit;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KioskController extends Controller
{
    /**
     * Public self-service kiosk page (no authentication required).
     */
    public function index(): View
    {
        $employees = Employee::active()->orderBy('name')->get();

        return view('kiosk.index', compact('employees'));
    }

    /**
     * Store a new visitor submission with default 'pending' status.
     */
    public function store(Request $request, WhatsAppService $whatsApp)
    {
        $data = $request->validate([
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_phone' => ['required', 'string', 'max:255'],
            'visitor_institution' => ['required', 'string', 'max:255'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'purpose' => ['required', 'string'],
            'visit_type' => ['required', 'in:meet,deliver,meeting_invitation'],
            'delivery_pref' => ['nullable', 'in:hand_in,leave'],
            'photo' => ['nullable', 'string'], // base64 data URL from webcam
        ]);

        // If the visitor is delivering something, the employee is optional.
        $employeeId = $data['employee_id'] ?? null;
        $deliveryPref = $data['visit_type'] === Visit::TYPE_DELIVER
            ? ($data['delivery_pref'] ?? null)
            : null;

        // Persist visitor photo (base64 data URL) into the public storage.
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
            'employee_id' => $employeeId ?? null,
            'purpose' => $data['purpose'],
            'visit_type' => $data['visit_type'],
            'delivery_pref' => $deliveryPref,
            'photo_path' => $photoPath,
            'qr_code_token' => Str::random(32),
            'status' => Visit::STATUS_PENDING,
        ]);

        $whatsApp->notifyEmployeeNewVisit($visit);

        // Blacklist alert: check name and institution against the blacklist list.
        $blacklistHit = Blacklist::query()
            ->where(function ($q) use ($data) {
                $q->where('name_or_institution', 'like', '%'.$data['visitor_name'].'%')
                    ->orWhere('name_or_institution', 'like', '%'.$data['visitor_institution'].'%');
            })
            ->first();

        $message = $blacklistHit
            ? 'Kunjungan Anda telah kami terima. Mohon menunggu dan segera laporkan diri ke meja resepsionis.'
            : 'Terima kasih! Kunjungan Anda telah kami terima. Silakan tunggu verifikasi dari resepsionis.';

        return response()->json([
            'visit' => $visit,
            'blacklisted' => (bool) $blacklistHit,
            'message' => $message,
        ]);
    }

    /**
     * Show the QR badge for an approved visit (used for printing / scanning).
     */
    public function badge(string $token)
    {
        $visit = Visit::where('qr_code_token', $token)->firstOrFail();

        return view('kiosk.badge', compact('visit'));
    }

    /**
     * Decode the base64 data-URL photo and persist it under storage/app/public/visits.
     */
    protected function storePhoto(string $dataUrl): ?string
    {
        // Strip the "data:image/...;base64," prefix.
        $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $dataUrl);

        if (! $base64 || ! Str::startsWith($dataUrl, 'data:image/')) {
            return null;
        }

        $image = base64_decode($base64, true);

        if ($image === false) {
            return null;
        }

        $filename = 'visits/'.Str::uuid().'.jpg';

        // The visits/photos live under storage/app/public/visits/photos
        $path = \Storage::disk('public')->put($filename, $image);

        if (! $path) {
            return null;
        }

        return $filename;
    }
}
