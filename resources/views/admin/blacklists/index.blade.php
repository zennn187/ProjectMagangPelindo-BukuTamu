@extends('layouts.operator')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Daftar Hitam</h1>
            <p class="text-sm text-gray-500">Nama atau instansi yang dilarang / diwaspadai.</p>
        </div>
        <a href="{{ route('admin.blacklists.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">+ Tambah</a>
    </div>

    <div class="rounded-xl glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/60 text-sm">
                <thead class="bg-white/40 text-left text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nama / Instansi</th>
                        <th class="px-5 py-3">Alasan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($blacklists as $blacklist)
                        <tr class="hover:bg-white/40">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ $blacklist->name_or_institution }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $blacklist->reason }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('admin.blacklists.edit', $blacklist) }}" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-200">Edit</a>
                                    <form method="POST" action="{{ route('admin.blacklists.destroy', $blacklist) }}" onsubmit="return confirm('Hapus entri ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-gray-500">Belum ada entri daftar hitam.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($blacklists->hasPages())
            <div class="px-5 py-3 border-t border-slate-200">{{ $blacklists->links() }}</div>
        @endif
    </div>
@endsection