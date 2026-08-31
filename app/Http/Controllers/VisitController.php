<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VisitController extends Controller
{
    /**
     * Receptionist/operator dashboard showing today's visits.
     */
    public function index(Request $request): View
    {
        $selectedDay = $request->date('day') ?? now()->toDateString();
        $search = trim((string) $request->input('search', ''));

        $query = Visit::with('employee')
            ->when($selectedDay, fn ($q) => $q->whereDate('created_at', $selectedDay))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('visitor_name', 'like', "%{$search}%")
                        ->orWhere('visitor_institution', 'like', "%{$search}%")
                        ->orWhere('visitor_phone', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhereHas('employee', fn ($employeeQuery) => $employeeQuery->where('name', 'like', "%{$search}%"));
                });
            });

        $visits = $query->orderBy('created_at', 'desc')->paginate(5)->appends($request->query());

        $countsQuery = Visit::query()
            ->when($selectedDay, fn ($q) => $q->whereDate('created_at', $selectedDay))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('visitor_name', 'like', "%{$search}%")
                        ->orWhere('visitor_institution', 'like', "%{$search}%")
                        ->orWhere('visitor_phone', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhereHas('employee', fn ($employeeQuery) => $employeeQuery->where('name', 'like', "%{$search}%"));
                });
            });

        $counts = [
            'pending' => (clone $countsQuery)->where('status', Visit::STATUS_PENDING)->count(),
            'waiting' => (clone $countsQuery)->where('status', Visit::STATUS_WAITING)->count(),
            'active' => (clone $countsQuery)->where('status', Visit::STATUS_ACTIVE)->count(),
            'completed' => (clone $countsQuery)->where('status', Visit::STATUS_COMPLETED)->count(),
            'rejected' => (clone $countsQuery)->where('status', Visit::STATUS_REJECTED)->count(),
        ];

        $weekly = collect(range(6, 0))->map(function (int $i) {
            $day = now()->subDays($i);

            return [
                'label' => $day->translatedFormat('D'),
                'total' => Visit::whereDate('created_at', $day)->count(),
                'completed' => Visit::whereDate('created_at', $day)->where('status', Visit::STATUS_COMPLETED)->count(),
            ];
        });

        $recentVisits = Visit::with('employee')
            ->when($selectedDay, fn ($q) => $q->whereDate('created_at', $selectedDay))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('reception.dashboard', compact('visits', 'counts', 'weekly', 'recentVisits', 'selectedDay', 'search'));
    }

    /**
     * Stream a visitor photo to authenticated reception and admin users.
     */
    public function photo(Visit $visit)
    {
        abort_unless($visit->photo_path && Storage::disk('public')->exists($visit->photo_path), 404);

        return response()->file(Storage::disk('public')->path($visit->photo_path));
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
