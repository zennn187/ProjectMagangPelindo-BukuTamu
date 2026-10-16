<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VisitsExport;
use App\Http\Controllers\Controller;
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

        $from = $request->filled('from') ? $request->date('from') : Carbon::today();
        $to = $request->filled('to') ? $request->date('to') : Carbon::today();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        if (! $request->filled('from') && ! $request->filled('to')) {
            $from = Carbon::today();
            $to = Carbon::today();
        }

        $baseQuery = \App\Models\Visit::query()->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);

        // Satu query aggregate untuk semua ringkasan (bukan 4 query count terpisah),
        // di-cache per rentang tanggal agar navigasi antar-menu tidak query ulang.
        $cacheKey = 'reports.summary.'.$from->toDateString().'.'.$to->toDateString();

        $summary = \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($baseQuery) {
            $row = (clone $baseQuery)
                ->selectRaw(
                    'COUNT(*) as total, SUM(status = ?) as pending, SUM(status = ?) as active, SUM(status = ?) as completed',
                    [\App\Models\Visit::STATUS_PENDING, \App\Models\Visit::STATUS_ACTIVE, \App\Models\Visit::STATUS_COMPLETED]
                )
                ->first();

            return [
                'total' => (int) ($row->total ?? 0),
                'pending' => (int) ($row->pending ?? 0),
                'active' => (int) ($row->active ?? 0),
                'completed' => (int) ($row->completed ?? 0),
            ];
        });

        // Chart harian: satu query GROUP BY (sebelumnya N query count — satu per hari).
        $countsByDay = \Illuminate\Support\Facades\Cache::remember($cacheKey.'.chart', 60, function () use ($baseQuery) {
            return (clone $baseQuery)
                ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->groupByRaw('DATE(created_at)')
                ->pluck('total', 'day');
        });

        $chartData = collect();

        $period = \Carbon\CarbonPeriod::create($from->copy()->startOfDay(), '1 day', $to->copy()->startOfDay());
        foreach ($period as $date) {
            $chartData->push([
                'label' => $date->translatedFormat('d'),
                'value' => (int) ($countsByDay[$date->toDateString()] ?? 0),
                'date' => $date->toDateString(),
            ]);
        }

        if ($chartData->isEmpty()) {
            $chartData->push([
                'label' => $from->translatedFormat('d'),
                'value' => 0,
                'date' => $from->toDateString(),
            ]);
        }

        $chartMax = $chartData->max('value') ?: 1;

        return view('admin.reports.index', compact('from', 'to', 'summary', 'chartData', 'chartMax'));
    }

    /**
     * Download the filtered visits as an Excel file.
     */
    public function export(Request $request): BinaryFileResponse
    {
        Gate::authorize('is-admin');

        $from = $request->date('from');
        $to = $request->date('to');

        return Excel::download(
            new VisitsExport($from, $to),
            'laporan-kunjungan-'.Carbon::now()->format('Ymd-His').'.xlsx'
        );
    }
}
