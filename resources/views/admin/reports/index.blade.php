@extends('layouts.operator')

@section('page_title', 'Laporan Kunjungan')

@section('content')
    <div class="mb-6">
        <p class="text-sm text-bank-gray">Rekap kunjungan dari database berdasarkan periode yang dipilih.</p>
    </div>

    <section class="mb-6" aria-label="Pilih periode laporan">
        <div class="mb-3">
            <h2 class="font-semibold text-bank-navy">Ringkasan Per Periode</h2>
            <p class="mt-1 text-xs text-bank-gray">Pilih kartu untuk melihat grafik dan rincian kunjungan pada rentang tersebut.</p>
        </div>
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach($periodCards as $key => $period)
                <a
                    href="{{ route('admin.reports.index', ['period' => $key]) }}"
                    aria-current="{{ $selectedPeriod === $key ? 'true' : 'false' }}"
                    data-report-period="{{ $key }}"
                    class="flex min-h-24 flex-col justify-between rounded-xl border bg-white px-4 py-3 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bank-blue {{ $selectedPeriod === $key ? 'border-bank-blue shadow-sm' : 'border-bank-light/70 hover:border-bank-blue/50' }}"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-flex rounded-md bg-bank-bg px-2 py-1 text-[10px] font-bold tracking-wide text-bank-gray">{{ $period['code'] }}</span>
                            <p class="mt-2 text-xs font-medium text-bank-gray">{{ $period['label'] }}</p>
                        </div>
                        <p class="text-lg font-semibold tabular-nums text-bank-navy">{{ number_format($period['total']) }}</p>
                    </div>
                    <p class="mt-2 text-[11px] text-bank-gray">
                        {{ $period['from']->translatedFormat('d M Y') }} – {{ $period['to']->translatedFormat('d M Y') }}
                    </p>
                </a>
            @endforeach
        </div>
    </section>

    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border border-bank-light/70 bg-white p-4">
            <p class="text-sm text-bank-gray">Total</p>
            <p class="mt-1 text-xl font-semibold tabular-nums text-bank-navy">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-xl border border-bank-light/70 bg-white p-4">
            <p class="text-sm text-bank-gray">Menunggu</p>
            <p class="mt-1 text-xl font-semibold tabular-nums text-bank-navy">{{ $summary['pending'] }}</p>
        </div>
        <div class="rounded-xl border border-bank-light/70 bg-white p-4">
            <p class="text-sm text-bank-gray">Di Dalam</p>
            <p class="mt-1 text-xl font-semibold tabular-nums text-bank-navy">{{ $summary['active'] }}</p>
        </div>
        <div class="rounded-xl border border-bank-light/70 bg-white p-4">
            <p class="text-sm text-bank-gray">Selesai</p>
            <p class="mt-1 text-xl font-semibold tabular-nums text-bank-navy">{{ $summary['completed'] }}</p>
        </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm mb-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-bank-navy">Rentang Tanggal Khusus</h2>
                <p class="mt-1 text-xs text-bank-gray">Pilih tanggal sendiri atau gunakan periode cepat.</p>
            </div>
            <nav class="inline-flex items-center gap-1 rounded-xl bg-bank-bg p-1" aria-label="Periode cepat laporan">
                @foreach($periodCards as $key => $period)
                    <a
                        href="{{ route('admin.reports.index', ['period' => $key]) }}"
                        title="{{ $period['label'] }}"
                        aria-label="{{ $period['label'] }}"
                        aria-current="{{ $selectedPeriod === $key ? 'true' : 'false' }}"
                        class="rounded-lg px-3 py-2 text-xs font-semibold transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bank-blue {{ $selectedPeriod === $key ? 'bg-bank-blue text-white shadow-sm' : 'text-bank-gray hover:bg-white hover:text-bank-navy' }}"
                    >{{ $period['code'] }}</a>
                @endforeach
            </nav>
        </div>
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="report-from" class="block text-sm font-medium text-bank-navy mb-1">Dari Tanggal</label>
                <input id="report-from" type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="soft-date">
            </div>
            <div>
                <label for="report-to" class="block text-sm font-medium text-bank-navy mb-1">Sampai Tanggal</label>
                <input id="report-to" type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="soft-date">
            </div>
            <div>
                <button type="submit" class="w-full rounded-xl bg-bank-blue px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">Tampilkan</button>
            </div>
            <div>
                <a href="{{ route('admin.reports.index') }}" class="w-full inline-flex justify-center rounded-xl bg-bank-light px-4 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/80">Reset</a>
            </div>
        </form>
    </div>

    <div class="rounded-3xl bg-white p-5 shadow-sm mb-6 sm:p-6">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-bank-navy">Grafik Kunjungan</h2>
                <p class="mt-1 text-xs text-bank-gray">{{ $from->translatedFormat('d M Y') }} – {{ $to->translatedFormat('d M Y') }}</p>
            </div>
            <form method="GET" action="{{ route('admin.reports.export') }}" target="_blank" class="inline-block" data-export-form="true">
                <input type="hidden" name="from" value="{{ $from->format('Y-m-d') }}">
                <input type="hidden" name="to" value="{{ $to->format('Y-m-d') }}">
                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Unduh Excel</button>
            </form>
        </div>

        <x-visit-bar-chart :data="$chartData" :max-value="$chartMax" aria-label="Jumlah kunjungan per tanggal" />
    </div>

    <section class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" aria-labelledby="report-visits-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
            <div>
                <h2 id="report-visits-heading" class="font-semibold text-bank-navy">Data Identitas Kunjungan</h2>
                <p class="mt-1 text-xs text-bank-gray">Daftar tamu untuk rentang {{ $from->translatedFormat('d M Y') }} – {{ $to->translatedFormat('d M Y') }}.</p>
            </div>
            <p class="text-xs text-bank-gray">{{ number_format($visits->total()) }} data</p>
        </div>

        @php
            $reportStatusLabels = [
                \App\Models\Visit::STATUS_PENDING => 'Menunggu',
                \App\Models\Visit::STATUS_WAITING => 'Menunggu',
                \App\Models\Visit::STATUS_ACTIVE => 'Di Dalam',
                \App\Models\Visit::STATUS_COMPLETED => 'Selesai',
                \App\Models\Visit::STATUS_REJECTED => 'Ditolak',
            ];
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-left text-sm">
                <thead>
                    <tr class="border-b border-bank-light text-[11px] uppercase tracking-wide text-bank-gray">
                        <th class="py-3 pr-4 font-medium">Waktu Daftar</th>
                        <th class="py-3 pr-4 font-medium">Foto</th>
                        <th class="py-3 pr-4 font-medium">Nama Tamu</th>
                        <th class="py-3 pr-4 font-medium">No. Identitas</th>
                        <th class="py-3 pr-4 font-medium">Telepon</th>
                        <th class="py-3 pr-4 font-medium">Instansi</th>
                        <th class="py-3 pr-4 font-medium">Bertemu Dengan</th>
                        <th class="py-3 pr-4 font-medium">Keperluan</th>
                        <th class="py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bank-light/70">
                    @forelse($visits as $visit)
                        @php
                            $reportPhotoUrl = ($visit->photo_exists || ($visit->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($visit->photo_path)))
                                ? route('visits.photo', ['visit' => $visit], false)
                                : null;
                        @endphp
                        <tr class="align-top text-bank-gray">
                            <td class="whitespace-nowrap py-3 pr-4 text-xs">{{ $visit->created_at?->format('d M Y, H:i') ?? '—' }}</td>
                            <td class="py-3 pr-4">
                                @if($reportPhotoUrl)
                                    <a
                                        href="{{ $reportPhotoUrl }}"
                                        target="_blank"
                                        rel="noopener"
                                        aria-label="Buka foto {{ $visit->visitor_name }}"
                                        class="inline-flex h-12 w-12 items-center justify-center overflow-hidden rounded-lg border border-bank-light bg-bank-bg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bank-blue"
                                    >
                                        <img src="{{ $reportPhotoUrl }}" alt="Foto {{ $visit->visitor_name }}" loading="lazy" decoding="async" class="h-full w-full object-cover">
                                    </a>
                                @else
                                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-lg border border-dashed border-bank-light text-[10px] text-bank-gray">Tidak ada</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 font-medium text-bank-navy">{{ $visit->visitor_name }}</td>
                            <td class="py-3 pr-4">{{ $visit->identity_number ?: '—' }}</td>
                            <td class="whitespace-nowrap py-3 pr-4">
                                @if($visit->visitor_phone)
                                    <a href="tel:{{ $visit->visitor_phone }}" class="font-medium text-bank-navy hover:underline">{{ $visit->visitor_phone }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 pr-4">{{ $visit->visitor_institution ?: '—' }}</td>
                            <td class="py-3 pr-4">{{ $visit->employee?->name ?: ($visit->person_to_meet ?: '—') }}</td>
                            <td class="max-w-xs whitespace-normal py-3 pr-4">{{ $visit->purpose ?: '—' }}</td>
                            <td class="py-3">
                                <span class="inline-flex rounded-full bg-bank-bg px-2.5 py-1 text-[11px] font-medium text-bank-gray">
                                    {{ $reportStatusLabels[$visit->status] ?? ucfirst($visit->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-sm text-bank-gray">Belum ada data kunjungan pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visits->hasPages())
            <div class="mt-4 border-t border-bank-light pt-4">
                {{ $visits->links() }}
            </div>
        @endif
    </section>
@endsection
