@extends('layouts.operator')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-500">Akun admin & resepsionis untuk mengakses sistem.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">+ Tambah Pengguna</a>
    </div>

    <div class="rounded-xl glass-card overflow-hidden">
        <table class="min-w-full divide-y divide-white/60 text-sm">
            <thead class="bg-white/40 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Peran</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr class="hover:bg-white/40">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-5 py-3">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $user->role === 'admin' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-500">Belum ada pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-slate-200">{{ $users->links() }}</div>
        @endif
    </div>
@endsection