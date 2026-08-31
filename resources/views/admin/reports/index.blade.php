@extends('layouts.operator')

@section('page_title', 'Laporan Kunjungan')

@section('content')
    <div class="mb-6">
        <p class="text-sm text-bank-gray">Rekap seluruh history kunjungan tamu.</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-bank-gray">Total</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-bank-gray">Menunggu</p>
            <p class="mt-1 text-2xl font-bold text-amber-600">{{ $summary['pending'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-bank-gray">Di Dalam</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $summary['active'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-bank-gray">Selesai</p>
            <p class="mt-1 text-2xl font-bold text-sky-600">{{ $summary['completed'] }}</p>
        </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-bank-navy mb-1">Dari Tanggal</label>
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="soft-date">
            </div>
            <div>
                <label class="block text-sm font-medium text-bank-navy mb-1">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="soft-date">
            </div>
            <div>
                <button type="submit" class="w-full rounded-xl bg-bank-blue px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">Tampilkan</button>
            </div>
            <div>
                <a href="{{ route('admin.reports.index') }}" class="w-full inline-flex justify-center rounded-xl bg-bank-light px-4 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/80">Reset</a>
            </div>
        </form>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-bank-navy">Grafik Kunjungan</h2>
            <form method="GET" action="{{ route('admin.reports.export') }}" target="_blank" class="inline-block" data-export-form="true">
                <input type="hidden" name="from" value="{{ $from->format('Y-m-d') }}">
                <input type="hidden" name="to" value="{{ $to->format('Y-m-d') }}">
                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Unduh Excel</button>
            </form>
        </div>

        <div class="flex h-52 items-end gap-2 sm:gap-3 pt-4">
            @foreach($chartData as $item)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <span class="text-[10px] text-bank-gray">{{ $item['value'] }}</span>
                    <div class="w-full rounded-t-xl bg-gradient-to-t from-bank-blue to-indigo-400 transition-all" style="height: {{ max(8, round(($item['value'] / $chartMax) * 100)) }}%"></div>
                    <span class="text-[10px] text-bank-gray">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
