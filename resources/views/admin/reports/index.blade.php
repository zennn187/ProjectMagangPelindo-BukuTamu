@extends('layouts.operator')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Laporan Kunjungan</h1>
            <p class="text-sm text-gray-500">Rekap seluruh history kunjungan tamu.</p>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl glass-card p-5">
            <p class="text-sm text-slate-500">Total</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-xl glass-card p-5">
            <p class="text-sm text-slate-500">Menunggu</p>
            <p class="mt-1 text-2xl font-bold text-amber-600">{{ $summary['pending'] }}</p>
        </div>
        <div class="rounded-xl glass-card p-5">
            <p class="text-sm text-slate-500">Di Dalam</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $summary['active'] }}</p>
        </div>
        <div class="rounded-xl glass-card p-5">
            <p class="text-sm text-slate-500">Selesai</p>
            <p class="mt-1 text-2xl font-bold text-sky-600">{{ $summary['completed'] }}</p>
        </div>
    </div>

    <!-- Filter + export -->
    <div class="rounded-xl glass-card p-6">
        <form method="GET" action="{{ route('admin.reports.export') }}" target="_blank" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Unduh Excel</button>
            </div>
            <div>
                <a href="{{ route('admin.reports.index') }}" class="w-full inline-flex justify-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">Reset</a>
            </div>
        </form>
    </div>
@endsection