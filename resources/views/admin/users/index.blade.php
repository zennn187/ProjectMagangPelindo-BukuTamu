@extends('layouts.operator')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-bank-navy">Manajemen Pengguna</h1>
            <p class="text-sm text-bank-gray">Akun admin & resepsionis untuk mengakses sistem.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-xl hover:shadow-bank-blue/30">
            + Tambah Pengguna
        </a>
    </div>

    <div class="rounded-3xl bg-white overflow-hidden shadow-sm ring-1 ring-bank-light/70">
        <table class="min-w-full divide-y divide-bank-light text-sm">
            <thead class="bg-bank-bg text-left text-xs uppercase text-bank-gray">
                <tr>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Peran</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-bank-light">
                @forelse($users as $user)
                    <tr class="transition-colors duration-200 hover:bg-bank-bg/70">
                        <td class="px-5 py-3 font-medium text-bank-navy">{{ $user->name }}</td>
                        <td class="px-5 py-3 text-bank-navy">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $user->role === 'admin' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg bg-bank-light px-3 py-1.5 text-xs font-medium text-bank-navy transition-colors hover:bg-bank-light/80">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Hapus pengguna ini? Akses akun akan dicabut." data-confirm-title="Hapus pengguna" data-confirm-action="Ya, hapus" data-confirm-variant="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-bank-red/10 px-3 py-1.5 text-xs font-medium text-bank-red transition-colors hover:bg-bank-red/20">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-bank-gray">Belum ada pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-bank-light">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
