<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VisitController extends Controller
{
    /**
     * Receptionist/operator dashboard showing today's visits.
     */
    public function index(): View
    {
        $todayVisits = Visit::with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Count from the same dataset as the table (all visits) so cards stay in sync.
        $counts = [
            'pending' => Visit::where('status', Visit::STATUS_PENDING)->count(),
            'waiting' => Visit::where('status', Visit::STATUS_WAITING)->count(),
            'active' => Visit::where('status', Visit::STATUS_ACTIVE)->count(),
            'completed' => Visit::where('status', Visit::STATUS_COMPLETED)->count(),
            'rejected' => Visit::where('status', Visit::STATUS_REJECTED)->count(),
        ];

        // Aktivitas 7 hari terakhir (untuk grafik batang dashboard).
        $weekly = collect(range(6, 0))->map(function (int $i) {
            $day = now()->subDays($i);

            return [
                'label' => $day->translatedFormat('D'),
                'total' => Visit::whereDate('created_at', $day)->count(),
                'completed' => Visit::whereDate('created_at', $day)->where('status', Visit::STATUS_COMPLETED)->count(),
            ];
        });

        // Ringkasan kunjungan terbaru untuk panel "Kunjungan Terbaru".
        $recentVisits = Visit::with('employee')->latest()->take(5)->get();

        return view('reception.dashboard', compact('todayVisits', 'counts', 'weekly', 'recentVisits'));
    }

    /**
     * Verify a visitor: pending|waiting -> active (Check-in).
     */
    public function checkIn(Request $request, Visit $visit, WhatsAppService $whatsApp): RedirectResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_PENDING, Visit::STATUS_WAITING]);

        $visit->update([
            'status' => Visit::STATUS_ACTIVE,
            'check_in_time' => now(),
        ]);

        $whatsApp->notifyVisitorAccepted($visit->fresh('employee'));

        return back()->with('success', 'Kunjungan berhasil diverifikasi (Check-in).');
    }

    /**
     * Put a pending visit into the "menunggu" state (the host is busy/cuti/away).
     */
    public function wait(Request $request, Visit $visit): RedirectResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_PENDING]);

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $visit->update([
            'status' => Visit::STATUS_WAITING,
            'status_note' => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Kunjungan diminta menunggu.');
    }

    /**
     * Complete a visit: active -> completed (Check-out).
     */
    public function checkOut(Request $request, Visit $visit): RedirectResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_ACTIVE]);

        $visit->update([
            'status' => Visit::STATUS_COMPLETED,
            'check_out_time' => now(),
        ]);

        return back()->with('success', 'Kunjungan selesai (Check-out).');
    }

    /**
     * Accept a delivery that the visitor chose to leave (titip) at the front desk.
     * The logged-in receptionist becomes the responsible receiver.
     */
    public function acceptDelivery(Request $request, Visit $visit, WhatsAppService $whatsApp): RedirectResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_PENDING]);

        if ($visit->visit_type !== Visit::TYPE_DELIVER || $visit->delivery_pref !== Visit::DELIVERY_LEAVE) {
            abort(422, 'Kunjungan ini bukan titipan ke resepsionis.');
        }

        $visit->update([
            'status' => Visit::STATUS_COMPLETED,
            'received_by_name' => auth()->user()->name,
            'check_in_time' => now(),
            'check_out_time' => now(),
        ]);

        $whatsApp->notifyVisitorAccepted($visit->fresh('employee'));

        return back()->with('success', 'Surat diterima dan dititipkan ke '.auth()->user()->name);
    }

    /**
     * Reject a pending|waiting visit.
     */
    public function reject(Request $request, Visit $visit): RedirectResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_PENDING, Visit::STATUS_WAITING]);

        $visit->update(['status' => Visit::STATUS_REJECTED]);

        return back()->with('success', 'Kunjungan ditolak.');
    }

    /**
     * Enforce operator access and that the visit is in one of the expected statuses.
     */
    protected function authorizeVisit(Visit $visit, array $expectedStatuses): void
    {
        abort_if(Gate::denies('is-receptionist-or-admin'), 403, 'Unauthorized.');

        if (! in_array($visit->status, $expectedStatuses, true)) {
            abort(422, 'Status kunjungan tidak valid untuk aksi ini.');
        }
    }
}
