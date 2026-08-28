<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of available services.
     */
    public function index(): View
    {
        $services = Service::orderBy('name')->paginate(12);

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $service->update($this->validated($request));

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
    }

    /**
     * Validation rules shared by store/update.
     */
    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:8'],
            'description' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}