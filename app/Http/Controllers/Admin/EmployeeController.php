<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees (admin only).
     */
    public function index(Request $request): View
    {
        Gate::authorize('is-admin');

        $query = Employee::query();
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'all');
        $department = trim((string) $request->input('department', ''));

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('is_active', $status === 'active');
        }

        if ($department !== '') {
            $query->where('department', 'like', "%{$department}%");
        }

        $employees = $query->orderBy('name')->paginate(10)->appends($request->query());

        return view('admin.employees.index', compact('employees', 'search', 'status', 'department'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        Gate::authorize('is-admin');

        return view('admin.employees.create');
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('is-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Employee::create($data + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('admin.employees.index')->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee): View
    {
        Gate::authorize('is-admin');

        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        Gate::authorize('is-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $employee->update($data + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('admin.employees.index')->with('success', 'Data pegawai diperbarui.');
    }

    /**
     * Remove the specified employee (visits keep history with null employee_id).
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        Gate::authorize('is-admin');

        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Pegawai dihapus.');
    }
}
