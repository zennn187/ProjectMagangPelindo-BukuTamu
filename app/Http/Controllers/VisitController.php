<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
        $selectedDay = $request->filled('day') ? $this->safeDay($request->input('day')) : now()->toDateString();
        $search = trim((string) $request->input('search', ''));

        $query = Visit::with('employee')->forDay($selectedDay)->search($search);

        $visits = $query->orderBy('created_at', 'desc')->paginate(5)->appends($request->query());

        $cacheKey = 'dashboard.counts.'.md5($selectedDay.'|'.$search);
        $counts = Cache::remember($cacheKey, 300, function () use ($selectedDay, $search) {
            $base = Visit::query()->forDay($selectedDay)->search($search);

            return [
                'pending' => (clone $base)->where('status', Visit::STATUS_PENDING)->count(),
                'waiting' => (clone $base)->where('status', Visit::STATUS_WAITING)->count(),
                'active' => (clone $base)->where('status', Visit::STATUS_ACTIVE)->count(),
                'completed' => (clone $base)->where('status', Visit::STATUS_COMPLETED)->count(),
                'rejected' => (clone $base)->where('status', Visit::STATUS_REJECTED)->count(),
            ];
        });

        $weekly = Cache::remember('dashboard.weekly', 300, function () {
            $rows = Visit::query()
                ->selectRaw('DATE(created_at) as d, COUNT(*) as total, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed', [Visit::STATUS_COMPLETED])
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('d')
                ->pluck('total', 'd');

            $rowsCompleted = Visit::query()
                ->selectRaw('DATE(created_at) as d, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed', [Visit::STATUS_COMPLETED])
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('d')
                ->pluck('completed', 'd');

            return collect(range(6, 0))->map(function (int $i) use ($rows, $rowsCompleted) {
                $day = now()->subDays($i);

                return [
                    'label' => $day->translatedFormat('D'),
                    'total' => (int) ($rows[$day->toDateString()] ?? 0),
                    'completed' => (int) ($rowsCompleted[$day->toDateString()] ?? 0),
                ];
            });
        });

        $recentVisits = Visit::with('employee')
            ->forDay($selectedDay)
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

        $selectedDay = $request->filled('day') ? $this->safeDay($request->input('day')) : now()->toDateString();
        $search = trim((string) $request->input('search', ''));

        $query = Visit::with('employee')->forDay($selectedDay)->search($search)->orderBy('created_at', 'desc');

        $paginator = $query->paginate(5)->appends($request->query());

        $visits = $paginator->getCollection()->map(function (Visit $visit) {
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
            'total' => $paginator->total(),
            'selected_day' => $selectedDay,
        ]);
    }

    /**
     * Verify a visitor: pending|waiting -> active (Check-in).
     */
    public function checkIn(Request $request, Visit $visit): RedirectResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_PENDING, Visit::STATUS_WAITING]);

        $visit->update([
            'status' => Visit::STATUS_ACTIVE,
            'check_in_time' => now(),
        ]);

        \App\Jobs\SendWhatsAppNotification::dispatch($visit->id, 'accepted');

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
    public function acceptDelivery(Request $request, Visit $visit): RedirectResponse
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

        \App\Jobs\SendWhatsAppNotification::dispatch($visit->id, 'accepted');

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

    protected function safeDay(?string $day): string
    {
        try {
            return Carbon::createFromFormat('Y-m-d', (string) $day)->format('Y-m-d');
        } catch (\Throwable) {
            try {
                return Carbon::parse((string) $day)->format('Y-m-d');
            } catch (\Throwable) {
                return now()->toDateString();
            }
        }
    }
}
