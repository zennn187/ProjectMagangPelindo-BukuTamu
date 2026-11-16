<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitStatusHistory;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VisitStatusHistoryController extends Controller
{
    public function index(): View
    {
        Gate::authorize('is-admin');

        $history = VisitStatusHistory::query()
            ->with(['visit:id,visitor_name,visitor_institution', 'user:id,name'])
            ->latest('created_at')
            ->paginate(50);

        return view('admin.status-history.index', compact('history'));
    }
}
