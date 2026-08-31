@extends('layouts.operator')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-bank-navy">Daftar Hitam</h1>
            <p class="text-sm text-bank-gray">Nama atau instansi yang dilarang / diwaspadai.</p>
        </div>
        <a href="{{ route('admin.blacklists.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-xl hover:shadow-bank-blue/30">
            + Tambah
        </a>
    </div>

    <div class="rounded-3xl bg-white overflow-hidden shadow-sm ring-1 ring-bank-light/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bank-light text-sm">
                <thead class="bg-bank-bg text-left text-xs uppercase text-bank-gray">
                    <tr>
                        <th class="px-5 py-3">Nama / Instansi</th>
                        <th class="px-5 py-3">Alasan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bank-light">
                    @forelse($blacklists as $blacklist)
                        <tr class="transition-colors duration-200 hover:bg-bank-bg/70">
                            <td class="px-5 py-3 font-medium text-bank-navy">{{ $blacklist->name_or_institution }}</td>
                            <td class="px-5 py-3 text-bank-navy">{{ $blacklist->reason }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.blacklists.edit', $blacklist) }}" class="rounded-lg bg-bank-light px-3 py-1.5 text-xs font-medium text-bank-navy transition-colors hover:bg-bank-light/80">Edit</a>
                                    <form method="POST" action="{{ route('admin.blacklists.destroy', $blacklist) }}" data-confirm="Hapus entri ini? Data blacklist akan dihapus dari sistem." data-confirm-title="Hapus entri" data-confirm-action="Ya, hapus" data-confirm-variant="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-bank-red/10 px-3 py-1.5 text-xs font-medium text-bank-red transition-colors hover:bg-bank-red/20">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-bank-gray">Belum ada entri daftar hitam.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($blacklists->hasPages())
            <div class="px-5 py-3 border-t border-bank-light">{{ $blacklists->links() }}</div>
        @endif
    </div>
@endsection
