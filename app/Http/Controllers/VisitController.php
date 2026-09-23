<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
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
        $selectedDay = $request->input('day');
        $selectedDay = $selectedDay ? Carbon::parse($selectedDay)->format('Y-m-d') : now()->toDateString();
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
     * Return the most recent visit records as JSON for live dashboard polling.
     */
    public function latest(Request $request): JsonResponse
    {
        Gate::authorize('is-receptionist-or-admin');

        $selectedDay = $request->input('day');
        $selectedDay = $selectedDay ? Carbon::parse($selectedDay)->format('Y-m-d') : now()->toDateString();
        $search = trim((string) $request->input('search', ''));

        $visits = Visit::with('employee')
            ->when($selectedDay, fn ($q) => $q->whereDate('created_at', $selectedDay))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('visitor_name', 'like', "%{$search}%")
                        ->orWhere('visitor_institution', 'like', "%{$search}%")
                        ->orWhere('visitor_phone', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhereHas('employee', fn ($employeeQuery) => $employeeQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function (Visit $visit) {
                return [
                    'id' => $visit->id,
                    'qr_code_token' => $visit->qr_code_token,
                    'visitor_name' => $visit->visitor_name,
                    'visitor_institution' => $visit->visitor_institution,
                    'visitor_phone' => $visit->visitor_phone,
                    'employee_name' => $visit->employee?->name ?? '—',
                    'purpose' => $visit->purpose,
                    'status' => $visit->status,
                    'status_note' => $visit->status_note,
                    'status_label' => [
                        'pending' => 'Belum Verifikasi',
                        'waiting' => 'Disuruh Menunggu',
                        'active' => 'Di Dalam',
                        'completed' => 'Selesai',
                        'rejected' => 'Ditolak',
                    ][$visit->status] ?? $visit->status,
                    'visit_type' => $visit->visit_type,
                    'delivery_pref' => $visit->delivery_pref,
                    'received_by_name' => $visit->received_by_name,
                    'created_at' => $visit->created_at->format('H:i'),
                    'photo_url' => $visit->photo_path && Storage::disk('public')->exists($visit->photo_path)
                        ? route('visits.photo', ['visit' => $visit], false)
                        : null,
                ];
            });

        return response()->json([
            'visits' => $visits,
            'total' => $visits->count(),
            'selected_day' => $selectedDay,
        ]);
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

        return back()->with('success', 'Kunjungan berhasil diverifikasi (Check-in).')->with('visit_action', [
            'type' => 'check-in',
            'visit_id' => $visit->id,
            'visitor_name' => $visit->visitor_name,
        ]);
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

        return back()->with('success', 'Kunjungan diminta menunggu.')->with('visit_action', [
            'type' => 'waiting',
            'visit_id' => $visit->id,
            'visitor_name' => $visit->visitor_name,
        ]);
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

        return back()->with('success', 'Kunjungan selesai (Check-out).')->with('visit_action', [
            'type' => 'check-out',
            'visit_id' => $visit->id,
            'visitor_name' => $visit->visitor_name,
        ]);
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

        return back()->with('success', 'Kunjungan ditolak.')->with('visit_action', [
            'type' => 'rejected',
            'visit_id' => $visit->id,
            'visitor_name' => $visit->visitor_name,
        ]);
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
