<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GateReportController extends Controller
{
    /**
     * Guest arrival report (visitors registered through the security gate)
     * with date filter, search, stats, and realtime updates.
     */
    public function index(Request $request): View
    {
        Gate::authorize('is-receptionist-or-admin');

        $selectedDay = $request->filled('day')
            ? $this->safeDay($request->input('day'))
            : now()->toDateString();
        $search = trim((string) $request->input('search', ''));

        $query = $this->gateQuery($selectedDay, $search);
        $visits = (clone $query)->orderBy('check_in_time', 'desc')->paginate(10)->appends($request->query());

        $base = $this->gateQuery($selectedDay, $search);

        $counts = [
            'total' => (clone $base)->count(),
            'inside' => (clone $base)->where('status', Visit::STATUS_ACTIVE)->count(),
            'completed' => (clone $base)->where('status', Visit::STATUS_COMPLETED)->count(),
        ];

        // Average visit duration for the selected day (in minutes) — cached aggregate query.
        $avgMinutes = Cache::remember(
            'gate.avg.'.md5($selectedDay.'|'.$search),
            120,
            fn () => (clone $base)
                ->where('status', Visit::STATUS_COMPLETED)
                ->whereNotNull('check_in_time')
                ->whereNotNull('check_out_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, check_in_time, check_out_time)) as avg_min')
                ->value('avg_min')
        );

        $counts['avg_duration'] = $avgMinutes
            ? sprintf('%d jam %02d menit', intdiv((int) $avgMinutes, 60), (int) $avgMinutes % 60)
            : '-';

        // QR code to be printed and stuck on the security glass — guests scan it
        // to open the gate registration form. Cached: same SVG for 24h.
        $glassQrSvg = Cache::remember(
            'gate.glass-qr',
            86400,
            fn () => QrCode::size(150)->color(9, 26, 58)->generate(route('gate.form'))
        );

        return view('admin.gate-reports', compact('visits', 'counts', 'selectedDay', 'search', 'glassQrSvg'));
    }

    /**
     * Realtime JSON feed polled by the report page: latest gate events,
     * live stats, and the current visitor list.
     */
    public function latest(Request $request): JsonResponse
    {
        Gate::authorize('is-receptionist-or-admin');

        $selectedDay = $request->filled('day')
            ? $this->safeDay($request->input('day'))
            : now()->toDateString();
        $search = trim((string) $request->input('search', ''));
        $since = $request->filled('since')
            ? $this->safeDateTime($request->input('since'))
            : now()->subMinutes(5);

        $query = $this->gateQuery($selectedDay, $search);
        $counts = [
            'total' => (clone $query)->count(),
            'inside' => (clone $query)->where('status', Visit::STATUS_ACTIVE)->count(),
            'completed' => (clone $query)->where('status', Visit::STATUS_COMPLETED)->count(),
        ];
        $visits = (clone $query)
            ->orderBy('check_in_time', 'desc')
            ->paginate(10, ['*'], 'page', max(1, (int) $request->input('page', 1)));

        $events = Visit::where('source', Visit::SOURCE_GATE)
            ->whereDate('check_in_time', $selectedDay)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('visitor_name', 'like', "%{$search}%")
                        ->orWhere('identity_number', 'like', "%{$search}%")
                        ->orWhere('visitor_institution', 'like', "%{$search}%")
                        ->orWhere('person_to_meet', 'like', "%{$search}%");
                });
            })
            ->where(function ($q) use ($since) {
                $q->where('updated_at', '>=', $since)->orWhere('created_at', '>=', $since);
            })
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn (Visit $v) => [
                'id' => $v->id,
                'name' => $v->visitor_name,
                'direction' => $v->check_out_time ? 'out' : 'in',
                'time' => ($v->check_out_time ?? $v->check_in_time ?? $v->created_at)?->format('H:i'),
                'duration' => $v->durationText(),
                'message' => $v->check_out_time
                    ? "Tamu atas nama {$v->visitor_name} sudah melakukan checkout pada jam {$v->check_out_time->format('H:i')}"
                    : "Tamu atas nama {$v->visitor_name} telah melakukan check-in pada jam ".($v->check_in_time?->format('H:i') ?? '-'),
            ])
            ->values();

        return response()->json([
            'server_time' => now()->format('H:i:s'),
            'counts' => $counts,
            'visits' => $visits->map(fn (Visit $v) => [
                'id' => $v->id,
                'name' => $v->visitor_name,
                'identity' => $v->identity_number,
                'institution' => $v->visitor_institution,
                'person_to_meet' => $v->person_to_meet,
                'check_in' => $v->check_in_time?->format('H:i'),
                'check_out' => $v->check_out_time?->format('H:i'),
                'duration' => $v->durationText(),
                'status' => $v->status,
                'chat_url' => $v->visitor_phone ? route('gate.chat', $v) : null,
            ]),
            'events' => $events,
            'last_updated_at' => now()->toISOString(),
        ]);
    }

    /**
     * Base query: gate-registered visits only, with optional day filter and search.
     */
    protected function gateQuery(?string $day, string $search = '')
    {
        return Visit::query()
            ->where('source', Visit::SOURCE_GATE)
            ->when($day, fn ($q) => $q->whereDate('check_in_time', $day))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('visitor_name', 'like', "%{$search}%")
                        ->orWhere('identity_number', 'like', "%{$search}%")
                        ->orWhere('visitor_institution', 'like', "%{$search}%")
                        ->orWhere('person_to_meet', 'like', "%{$search}%");
                });
            });
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

    protected function safeDateTime(?string $value): Carbon
    {
        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable) {
            return now()->subMinutes(5);
        }
    }
}
