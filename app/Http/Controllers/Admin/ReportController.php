<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VisitsExport;
use App\Http\Controllers\Controller;
use App\Models\Visit;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    /**
     * Report page with date filtering (admin only).
     */
    public function index(Request $request): View
    {
        Gate::authorize('is-admin');

        $periods = $this->periods();
        $range = $this->resolveRange($request, $periods);
        $from = $range['from'];
        $to = $range['to'];
        $selectedPeriod = $range['period'];

        $baseQuery = Visit::query()
            ->whereBetween('created_at', [$from, $to]);

        $row = (clone $baseQuery)
            ->selectRaw(
                'COUNT(*) as total, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed',
                [
                    Visit::STATUS_PENDING,
                    Visit::STATUS_ACTIVE,
                    Visit::STATUS_COMPLETED,
                ]
            )
            ->first();

        $summary = [
            'total' => (int) ($row->total ?? 0),
            'pending' => (int) ($row->pending ?? 0),
            'active' => (int) ($row->active ?? 0),
            'completed' => (int) ($row->completed ?? 0),
        ];

        $countsByDay = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'day');

        $chartData = collect();

        $days = CarbonPeriod::create($from->copy()->startOfDay(), '1 day', $to->copy()->startOfDay());
        foreach ($days as $date) {
            $chartData->push([
                'label' => $date->translatedFormat('d'),
                'value' => (int) ($countsByDay[$date->toDateString()] ?? 0),
                'date' => $date->toDateString(),
            ]);
        }

        $periodCards = collect($periods)->map(function (array $period) {
            $periodStart = $period['from']->copy()->startOfDay();
            $periodEnd = $period['to']->copy()->endOfDay();

            return [
                ...$period,
                'total' => Visit::query()
                    ->whereBetween('created_at', [$periodStart, $periodEnd])
                    ->count(),
            ];
        });

        $visits = Visit::query()
            ->with('employee')
            ->withExists('photo')
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        if ($chartData->isEmpty()) {
            $chartData->push([
                'label' => $from->translatedFormat('d'),
                'value' => 0,
                'date' => $from->toDateString(),
            ]);
        }

        $chartMax = $chartData->max('value') ?: 1;

        return view('admin.reports.index', compact(
            'from',
            'to',
            'summary',
            'chartData',
            'chartMax',
            'periodCards',
            'selectedPeriod',
            'visits',
        ));
    }

    /**
     * Download the filtered visits as an Excel file.
     */
    public function export(Request $request): BinaryFileResponse
    {
        Gate::authorize('is-admin');

        $range = $this->resolveRange($request, $this->periods());
        $from = $range['from'];
        $to = $range['to'];

        return Excel::download(
            new VisitsExport($from, $to),
            'laporan-kunjungan-'.$from->toDateString().'-sampai-'.$to->toDateString().'-'.Carbon::now()->format('Ymd-His').'.xlsx'
        );
    }

    /**
     * @return array<string, array{code: string, label: string, from: Carbon, to: Carbon}>
     */
    private function periods(): array
    {
        $today = Carbon::today();

        return [
            'week' => [
                'code' => '1W',
                'label' => 'Seminggu Terakhir',
                'from' => $today->copy()->subDays(6),
                'to' => $today->copy(),
            ],
            'month' => [
                'code' => '1M',
                'label' => 'Sebulan Terakhir',
                'from' => $today->copy()->subMonthNoOverflow(),
                'to' => $today->copy(),
            ],
            'six_months' => [
                'code' => '6M',
                'label' => '6 Bulan Terakhir',
                'from' => $today->copy()->subMonthsNoOverflow(6),
                'to' => $today->copy(),
            ],
            'year' => [
                'code' => '1Y',
                'label' => 'Setahun Terakhir',
                'from' => $today->copy()->subMonthsNoOverflow(12),
                'to' => $today->copy(),
            ],
        ];
    }

    /**
     * @param  array<string, array{code: string, label: string, from: Carbon, to: Carbon}>  $periods
     * @return array{from: Carbon, to: Carbon, period: ?string}
     */
    private function resolveRange(Request $request, array $periods): array
    {
        $selectedPeriod = null;

        if (! $request->filled('from') && ! $request->filled('to')) {
            $requestedPeriod = (string) $request->input('period', '');
            if (isset($periods[$requestedPeriod])) {
                $selectedPeriod = $requestedPeriod;
                $from = $periods[$requestedPeriod]['from']->copy();
                $to = $periods[$requestedPeriod]['to']->copy();
            }
        }

        if (! isset($from, $to)) {
            $from = $request->filled('from') ? $request->date('from') : Carbon::today();
            $to = $request->filled('to') ? $request->date('to') : Carbon::today();
        }

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        return [
            'from' => $from->startOfDay(),
            'to' => $to->endOfDay(),
            'period' => $selectedPeriod,
        ];
    }
}
