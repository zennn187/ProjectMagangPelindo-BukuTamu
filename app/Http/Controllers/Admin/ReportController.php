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

        $from = $request->date('from') ?? Carbon::today()->startOfMonth();
        $to = $request->date('to') ?? Carbon::today();

        // Simple totals for the summary cards.
        $summary = [
            'total' => \App\Models\Visit::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])->count(),
            'pending' => \App\Models\Visit::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])->where('status', \App\Models\Visit::STATUS_PENDING)->count(),
            'active' => \App\Models\Visit::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])->where('status', \App\Models\Visit::STATUS_ACTIVE)->count(),
            'completed' => \App\Models\Visit::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])->where('status', \App\Models\Visit::STATUS_COMPLETED)->count(),
        ];

        return view('admin.reports.index', compact('from', 'to', 'summary'));
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
