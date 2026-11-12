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

        $query = Visit::with('employee')->withExists('photo')->forDay($selectedDay)->search($search);

        $visits = $query->orderBy('created_at', 'desc')->paginate(5)->appends($request->query());

        $counts = $this->dashboardCounts($selectedDay, $search);

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

        $recentVisits = Visit::with('employee')->withExists('photo')
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

        $visit->update([
            'status' => Visit::STATUS_ACTIVE,
            'check_in_time' => now(),
        ]);

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

        $visit->update([
            'status' => Visit::STATUS_WAITING,
            'status_note' => $data['note'] ?? null,
        ]);

        return $this->actionResponse($request, $visit, 'waiting', 'Kunjungan diminta menunggu.');
    }

    /**
     * Complete a visit: active -> completed (Check-out).
     */
    public function checkOut(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_ACTIVE]);

        $visit->update([
            'status' => Visit::STATUS_COMPLETED,
            'check_out_time' => now(),
        ]);

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

        $visit->update([
            'status' => Visit::STATUS_COMPLETED,
            'received_by_name' => auth()->user()->name,
            'check_in_time' => now(),
            'check_out_time' => now(),
        ]);

        \App\Jobs\SendWhatsAppNotification::dispatch($visit->id, 'accepted')->afterResponse();

        return back()->with('success', 'Surat diterima dan dititipkan ke '.auth()->user()->name);
    }

    /**
     * Reject a pending|waiting visit.
     */
    public function reject(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        $this->authorizeVisit($visit, [Visit::STATUS_PENDING, Visit::STATUS_WAITING]);

        $visit->update(['status' => Visit::STATUS_REJECTED]);

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
