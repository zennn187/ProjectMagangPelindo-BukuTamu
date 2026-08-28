@extends('layouts.operator')

@section('page_title', 'Layanan')

@section('content')
    @php
        $pastels = [
            'bg-blue-50 text-blue-600',
            'bg-emerald-50 text-emerald-600',
            'bg-amber-50 text-amber-600',
            'bg-rose-50 text-rose-500',
            'bg-violet-50 text-violet-600',
            'bg-sky-50 text-sky-600',
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <p class="text-sm text-bank-gray">Layanan pelayanan yang tersedia untuk tamu di kantor.</p>
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.services.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 transition-colors">
                + Tambah Layanan
            </a>
        @endif
    </div>

    {{-- Kartu layanan --}}
    <div class="grid grid-cols-2 md:grid-cols-3 2xl:grid-cols-5 gap-4 sm:gap-5 mb-8">
        @forelse($services as $index => $service)
            <div class="bg-white rounded-3xl p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center h-11 w-11 rounded-full text-lg {{ $pastels[$index % count($pastels)] }}">{{ $service->icon }}</div>
                <p class="mt-4 text-sm font-semibold text-bank-navy truncate" title="{{ $service->name }}">{{ $service->name }}</p>
                <p class="mt-1 text-xs text-bank-gray line-clamp-2" title="{{ $service->description }}">{{ $service->description ?: '—' }}</p>
                @if($service->note)
                    <p class="mt-2 text-[11px] text-amber-600 line-clamp-2" title="{{ $service->note }}">✎ {{ $service->note }}</p>
                @endif
                @unless($service->is_active)
                    <span class="mt-2 inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium bg-bank-light text-bank-gray">Non-aktif</span>
                @endunless
            </div>
@empty
            <div class="col-span-full bg-white rounded-3xl p-8 text-center text-bank-gray shadow-sm">
                Belum ada layanan.
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('admin.services.create') }}" class="text-bank-blue font-medium hover:underline">Tambahkan layanan pertama</a>
                @endif
            </div>
        @endforelse
    </div>
{{-- Tabel daftar layanan --}}
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm">
        <div class="px-5 sm:px-6 py-5">
            <h2 class="font-semibold text-bank-navy">Daftar Layanan</h2>
            <p class="text-xs text-bank-gray mt-0.5">Total {{ $services->total() }} layanan terdaftar</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bank-light text-sm">
                <thead class="bg-bank-bg text-left text-xs uppercase text-bank-gray">
                    <tr>
                        <th class="px-5 py-3">Layanan</th>
                        <th class="px-5 py-3">Deskripsi</th>
                        <th class="px-5 py-3 hidden md:table-cell">Catatan</th>
                        <th class="px-5 py-3">Status</th>
                        @if(auth()->user()?->isAdmin())
                            <th class="px-5 py-3 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-bank-light">
                    @forelse($services as $service)
                        <tr class="hover:bg-bank-bg/60 align-top">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex items-center justify-center h-9 w-9 rounded-full bg-bank-light text-base shrink-0">{{ $service->icon }}</span>
                                    <span class="font-medium text-bank-navy">{{ $service->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-bank-navy max-w-[220px]"><div class="truncate" title="{{ $service->description }}">{{ $service->description ?: '—' }}</div></td>
                            <td class="px-5 py-3 text-bank-navy hidden md:table-cell max-w-[260px]"><div class="truncate" title="{{ $service->note }}">{{ $service->note ?: '—' }}</div></td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $service->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-bank-light text-bank-gray' }}">
                                    {{ $service->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            @if(auth()->user()?->isAdmin())
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('admin.services.edit', $service) }}" class="rounded-lg bg-bank-light px-3 py-1.5 text-xs font-medium text-bank-navy hover:bg-bank-light/70">Edit</a>
                                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Hapus layanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg bg-bank-red/10 px-3 py-1.5 text-xs font-medium text-bank-red hover:bg-bank-red/20">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-bank-gray">Belum ada layanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($services->hasPages())
            <div class="px-5 py-3 border-t border-bank-light">{{ $services->links() }}</div>
        @endif
    </div>
@endsection