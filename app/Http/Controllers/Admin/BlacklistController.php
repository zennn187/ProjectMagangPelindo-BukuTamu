<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BlacklistController extends Controller
{
    /**
     * Display the list of blacklisted names/institutions.
     */
    public function index(): View
    {
        Gate::authorize('is-admin');

        $blacklists = Blacklist::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.blacklists.index', compact('blacklists'));
    }

    /**
     * Show the form to create a new blacklist entry.
     */
    public function create(): View
    {
        Gate::authorize('is-admin');

        return view('admin.blacklists.create');
    }

    /**
     * Store a new blacklist entry.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('is-admin');

        $data = $request->validate([
            'name_or_institution' => ['required', 'string', 'max:255'],
            'reason' => ['required', 'string'],
        ]);

        Blacklist::create($data);

        return redirect()->route('admin.blacklists.index')->with('success', 'Entri daftar hitam ditambahkan.');
    }

    /**
     * Show the edit form for a blacklist entry.
     */
    public function edit(Blacklist $blacklist): View
    {
        Gate::authorize('is-admin');

        return view('admin.blacklists.edit', compact('blacklist'));
    }

    /**
     * Update a blacklist entry.
     */
    public function update(Request $request, Blacklist $blacklist): RedirectResponse
    {
        Gate::authorize('is-admin');

        $data = $request->validate([
            'name_or_institution' => ['required', 'string', 'max:255'],
            'reason' => ['required', 'string'],
        ]);

        $blacklist->update($data);

        return redirect()->route('admin.blacklists.index')->with('success', 'Entri daftar hitam diperbarui.');
    }

    /**
     * Delete a blacklist entry.
     */
    public function destroy(Blacklist $blacklist): RedirectResponse
    {
        Gate::authorize('is-admin');

        $blacklist->delete();

        return redirect()->route('admin.blacklists.index')->with('success', 'Entri daftar hitam dihapus.');
    }
}
