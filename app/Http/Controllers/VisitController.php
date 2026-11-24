<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\VisitStatusService;
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
        Gate::authorize('is-receptionist-or-admin');

        $selectedDay = $request->filled('day') ? $this->safeDay($request->input('day')) : now()->toDateString();
        $search = trim((string) $request->input('search', ''));
        $maxChartWeek = now()->daysInMonth > 28 ? 5 : 4;
        $chartWeek = filter_var($request->query('chart_week'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => $maxChartWeek],
        ]) ?: min($maxChartWeek, (int) ceil(now()->day / 7));

        $query = Visit::with('employee')->withExists('photo')->forDay($selectedDay)->search($search);

        $visits = $query->orderBy('created_at', 'desc')->paginate(5)->appends($request->query());

        $counts = $this->dashboardCounts($selectedDay, $search);

        $openVisitStatuses = [Visit::STATUS_PENDING, Visit::STATUS_WAITING, Visit::STATUS_ACTIVE];
        $openVisits = Visit::with('employee')->withExists('photo')
            ->whereIn('status', $openVisitStatuses)
            ->orderBy('created_at')
            ->paginate(5, ['*'], 'open_page')
            ->appends($request->query());
        $openVisitStatusCounts = Visit::query()
            ->whereIn('status', $openVisitStatuses)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');
        $openVisitCounts = [
            'pending' => (int) ($openVisitStatusCounts[Visit::STATUS_PENDING] ?? 0),
            'waiting' => (int) ($openVisitStatusCounts[Visit::STATUS_WAITING] ?? 0),
            'active' => (int) ($openVisitStatusCounts[Visit::STATUS_ACTIVE] ?? 0),
        ];
        $openVisitCounts['total'] = array_sum($openVisitCounts);

        $monthStart = now()->startOfMonth();
        $monthly = Cache::remember('dashboard.monthly.'.$monthStart->format('Y-m'), 300, function () use ($monthStart) {
            $monthEnd = $monthStart->copy()->endOfMonth();
            $rows = Visit::query()
                ->selectRaw('DATE(created_at) as d, COUNT(*) as total')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->groupBy('d')
                ->pluck('total', 'd');

            return collect(range(1, $monthEnd->day))->map(function (int $dayNumber) use ($monthStart, $rows) {
                $day = $monthStart->copy()->day($dayNumber);

                return [
                    'date' => $day->toDateString(),
                    'day' => $dayNumber,
                    'label' => $day->translatedFormat('D'),
                    'total' => (int) ($rows[$day->toDateString()] ?? 0),
                ];
            });
        });

        $recentVisits = $search === '' && $visits->currentPage() === 1
            ? $visits->getCollection()
            : Visit::with('employee')->withExists('photo')
                ->forDay($selectedDay)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

        return view('reception.dashboard', compact(
            'visits',
            'counts',
            'monthly',
            'recentVisits',
            'selectedDay',
            'search',
            'chartWeek',
            'openVisits',
            'openVisitCounts'
        ));
    }

    /**
     * Stream a visitor photo to authenticated reception and admin users.
     */
    public function photo(Visit $visit)
    {
        Gate::authorize('is-receptionist-or-admin');

        $photo = $visit->photo()->first();

        if ($photo) {
            return response($photo->contents, 200, [
                'Content-Type' => $photo->mime_type,
                'Cache-Control' => 'private, max-age=3600, immutable',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        abort_unless($visit->photo_path && Storage::disk('public')->exists($visit->photo_path), 404);

        return response()->file(Storage::disk('public')->path($visit->photo_path), [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Return the most recent visit records as JSON for live dashboard polling.
     */
    public function latest(Request $request): JsonResponse
    {
        Gate::authorize('is-receptionist-or-admin');

        $selectedDay = $request->filled('day') ? $this->safeDay($request->input('day')) : now()->toDateString();
        $search = trim((string) $request->input('search', ''));

        $query = Visit::with('employee')->withExists('photo')->forDay($selectedDay)->search($search)->orderBy('created_at', 'desc');

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
                    'photo_url' => ($visit->photo_exists || ($visit->photo_path && Storage::disk('public')->exists($visit->photo_path)))
                        ? route('visits.photo', ['visit' => $visit], false)
                        : null,
                ];
            });

        return response()->json([
            'visits' => $visits,
            'total' => $paginator->total(),
            'selected_day' => $selectedDay,
            'counts' => $this->dashboardCounts($selectedDay, $search),
        ]);
    }

    /**
     * Verify a visitor: pending|waiting -> active (Check-in).
     */
    public function checkIn(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_PENDING, Visit::STATUS_WAITING]);

        $visit = app(VisitStatusService::class)->transition(
            $visit,
            [Visit::STATUS_PENDING, Visit::STATUS_WAITING],
            Visit::STATUS_ACTIVE,
            'check-in',
            ['check_in_time' => now()],
        );

        \App\Jobs\SendWhatsAppNotification::dispatch($visit->id, 'accepted')->afterResponse();

        return $this->actionResponse($request, $visit, 'check-in', 'Kunjungan berhasil diverifikasi (Check-in).');
    }

    /**
     * Put a pending visit into the "menunggu" state (the host is busy/cuti/away).
     */
    public function wait(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_PENDING]);

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $visit = app(VisitStatusService::class)->transition(
            $visit,
            [Visit::STATUS_PENDING],
            Visit::STATUS_WAITING,
            'waiting',
            ['status_note' => $data['note'] ?? null],
            $data['note'] ?? null,
        );

        return $this->actionResponse($request, $visit, 'waiting', 'Kunjungan diminta menunggu.');
    }

    /**
     * Complete a visit: active -> completed (Check-out).
     */
    public function checkOut(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_ACTIVE]);

        $visit = app(VisitStatusService::class)->transition(
            $visit,
            [Visit::STATUS_ACTIVE],
            Visit::STATUS_COMPLETED,
            'check-out',
            ['check_out_time' => now()],
        );

        return $this->actionResponse($request, $visit, 'check-out', 'Kunjungan selesai (Check-out).');
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

        $visit = app(VisitStatusService::class)->transition(
            $visit,
            [Visit::STATUS_PENDING],
            Visit::STATUS_COMPLETED,
            'accept-delivery',
            [
                'received_by_name' => auth()->user()->name,
                'check_in_time' => now(),
                'check_out_time' => now(),
            ],
        );

        \App\Jobs\SendWhatsAppNotification::dispatch($visit->id, 'accepted')->afterResponse();

        return back()->with('success', 'Surat diterima dan dititipkan ke '.auth()->user()->name);
    }

    /**
     * Reject a pending|waiting visit.
     */
    public function reject(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_PENDING, Visit::STATUS_WAITING]);

        $visit = app(VisitStatusService::class)->transition(
            $visit,
            [Visit::STATUS_PENDING, Visit::STATUS_WAITING],
            Visit::STATUS_REJECTED,
            'rejected',
        );

        return $this->actionResponse($request, $visit, 'rejected', 'Kunjungan ditolak.');
    }

    protected function actionResponse(Request $request, Visit $visit, string $type, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'visit_id' => $visit->id,
                'visitor_name' => $visit->visitor_name,
                'status' => $visit->status,
            ]);
        }

        return back()->with('success', $message)->with('visit_action', [
            'type' => $type,
            'visit_id' => $visit->id,
            'visitor_name' => $visit->visitor_name,
        ]);
    }

    protected function dashboardCounts(string $selectedDay, string $search): array
    {
        $totals = Visit::query()
            ->forDay($selectedDay)
            ->search($search)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'pending' => (int) ($totals[Visit::STATUS_PENDING] ?? 0),
            'waiting' => (int) ($totals[Visit::STATUS_WAITING] ?? 0),
            'active' => (int) ($totals[Visit::STATUS_ACTIVE] ?? 0),
            'completed' => (int) ($totals[Visit::STATUS_COMPLETED] ?? 0),
            'rejected' => (int) ($totals[Visit::STATUS_REJECTED] ?? 0),
        ];
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
