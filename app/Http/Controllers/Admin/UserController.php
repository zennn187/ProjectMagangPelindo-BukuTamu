<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display the list of internal users (admin & receptionists).
     */
    public function index(): View
    {
        Gate::authorize('is-admin');

        $users = User::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form to create a new user.
     */
    public function create(): View
    {
        Gate::authorize('is-admin');

        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('is-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,receptionist'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // hashed automatically by the cast
            'role' => $data['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dibuat.');
    }

    /**
     * Show the form to edit a user.
     */
    public function edit(User $user): View
    {
        Gate::authorize('is-admin');

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update a user (role, name, email, optional password).
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('is-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,receptionist'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna diperbarui.');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('is-admin');

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna dihapus.');
    }
}
