@extends('layouts.operator')

@section('page_title', 'Dashboard')

@section('content')
    @php
        $statusColor = [
            'pending'   => 'bg-amber-100 text-amber-700',
            'waiting'   => 'bg-orange-100 text-orange-700',
            'active'    => 'bg-emerald-100 text-emerald-700',
            'completed' => 'bg-sky-100 text-sky-700',
            'rejected'  => 'bg-red-100 text-red-700',
        ];
        $statusLabel = [
            'pending'   => 'Belum Verifikasi',
            'waiting'   => 'Disuruh Menunggu',
            'active'    => 'Di Dalam',
            'completed' => 'Selesai',
            'rejected'  => 'Ditolak',
        ];
        $typeLabel = [
            'meet' => 'Kunjungan',
            'deliver' => 'Pengantaran Surat',
            'meeting_invitation' => 'Undangan Rapat / Kegiatan',
        ];
        $prefLabel = ['hand_in' => 'Diantar langsung ke yang dituju', 'leave' => 'Dititipkan ke resepsionis'];
        $maxWeekly = $weekly->max('total') ?: 1;
        $cards = [
            ['label' => 'Menunggu Verifikasi', 'value' => $counts['pending'], 'icon' => '⏳', 'ring' => 'bg-amber-50 text-amber-600'],
            ['label' => 'Disuruh Menunggu', 'value' => $counts['waiting'], 'icon' => '🕐', 'ring' => 'bg-orange-50 text-orange-600'],
            ['label' => 'Di Dalam', 'value' => $counts['active'], 'icon' => '🧍', 'ring' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Selesai', 'value' => $counts['completed'], 'icon' => '✅', 'ring' => 'bg-sky-50 text-sky-600'],
            ['label' => 'Ditolak', 'value' => $counts['rejected'], 'icon' => '🚫', 'ring' => 'bg-red-50 text-red-500'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <p class="text-sm text-bank-gray">Pantau tamu secara real-time dan lakukan verifikasi.</p>
        <a href="{{ route('kiosk') }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 transition-colors">
            Buka Kiosk
        </a>
    </div>

    {{-- Kartu statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-3 2xl:grid-cols-5 gap-4 sm:gap-5 mb-6">
        @foreach($cards as $card)
            <div class="bg-white rounded-3xl p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center h-11 w-11 rounded-full {{ $card['ring'] }} text-lg">{{ $card['icon'] }}</div>
                <p class="mt-4 text-sm font-medium text-bank-gray truncate">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-bank-navy">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Grafik + kunjungan terbaru --}}
    <div class="grid lg:grid-cols-3 gap-4 sm:gap-5 mb-6">
        <div class="lg:col-span-2 bg-white rounded-3xl p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-bank-navy">Aktivitas 7 Hari</h2>
                <span class="text-xs text-bank-gray">Total kunjungan per hari</span>
            </div>
            <div class="flex items-end justify-between gap-2 sm:gap-4 h-44">
                @foreach($weekly as $day)
                    <div class="flex-1 flex flex-col items-center gap-2 h-full">
                        <span class="text-[10px] sm:text-xs font-medium text-bank-navy">{{ $day['total'] }}</span>
                        <div class="w-full max-w-[34px] flex-1 flex items-end rounded-xl bg-bank-light/50 overflow-hidden">
                            <div class="w-full rounded-xl bg-gradient-to-t from-bank-blue to-indigo-400 transition-all"
                                 style="height: {{ max(4, round($day['total'] / $maxWeekly * 100)) }}%"></div>
                        </div>
                        <span class="text-[10px] sm:text-xs text-bank-gray">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-bank-navy">Kunjungan Terbaru</h2>
            </div>
            <ul class="space-y-4">
                @forelse($recentVisits as $visit)
                    <li class="flex items-center gap-3">
                        <div class="h-10 w-10 shrink-0 rounded-full grid place-items-center font-semibold uppercase
                            {{ ['pending' => 'bg-amber-100 text-amber-600', 'waiting' => 'bg-orange-100 text-orange-600', 'active' => 'bg-emerald-100 text-emerald-600', 'completed' => 'bg-sky-100 text-sky-600', 'rejected' => 'bg-red-100 text-red-500'][$visit->status] ?? 'bg-bank-light text-bank-blue' }}">
                            {{ strtoupper(substr($visit->visitor_name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-bank-navy truncate">{{ $visit->visitor_name }}</p>
                            <p class="text-xs text-bank-gray truncate">ke {{ $visit->employee->name ?? '—' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs text-bank-gray">{{ $visit->created_at->format('H:i') }}</p>
                            <span class="inline-block h-2 w-2 rounded-full mt-1 {{ ['pending' => 'bg-amber-400', 'waiting' => 'bg-orange-400', 'active' => 'bg-emerald-400', 'completed' => 'bg-sky-400', 'rejected' => 'bg-red-400'][$visit->status] ?? 'bg-bank-gray' }}"></span>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-bank-gray py-6 text-center">Belum ada kunjungan.</li>
                @endforelse
            </ul>
        </div>
    </div>
{{-- Tabel daftar kunjungan --}}
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm">
        <div class="px-5 sm:px-6 py-5 flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-semibold text-bank-navy">Daftar Kunjungan</h2>
            <span class="text-xs text-bank-gray">{{ $todayVisits->total() }} total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bank-light text-sm">
                <thead class="bg-bank-bg text-left text-xs uppercase text-bank-gray">
                    <tr>
                        <th class="px-4 sm:px-5 py-3">Tamu</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Jam</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 hidden md:table-cell">Keperluan / Catatan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bank-light">
                    @forelse($todayVisits as $visit)
                        <tr class="hover:bg-bank-bg/60 align-top">
                            <td class="px-4 sm:px-5 py-3">
                                <div class="font-medium text-bank-navy">{{ $visit->visitor_name }}</div>
                                <div class="text-xs text-bank-gray">{{ $visit->visitor_institution }}</div>
                                <div class="text-xs text-bank-gray/80">{{ $visit->visitor_phone }}</div>
                                <div class="text-xs text-bank-gray mt-1">
                                    ke: <span class="font-medium">{{ $visit->employee->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-bank-navy">
                                <div>{{ $typeLabel[$visit->visit_type] ?? 'Kunjungan' }}</div>
                                @if($visit->visit_type === 'deliver')
                                    <div class="text-[11px] text-bank-gray mt-0.5">{{ $prefLabel[$visit->delivery_pref] ?? '' }}</div>
                                    @if($visit->delivery_pref === 'leave' && $visit->received_by_name)
                                        <div class="text-[11px] text-bank-green font-medium mt-0.5">
                                            Diterima oleh: {{ $visit->received_by_name }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-bank-navy hidden sm:table-cell whitespace-nowrap">
                                {{ $visit->created_at->format('H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColor[$visit->status] ?? 'bg-bank-light text-bank-navy' }}">
                                    {{ $statusLabel[$visit->status] ?? $visit->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-bank-navy hidden md:table-cell max-w-[240px]">
                                <div class="truncate" title="{{ $visit->purpose }}">{{ $visit->purpose }}</div>
                                @if($visit->status_note)
                                    <div class="text-[11px] text-bank-yellow mt-1" title="{{ $visit->status_note }}">✎ {{ $visit->status_note }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-1.5">
@if($visit->status === 'pending')
                                        <form method="POST" action="{{ route('visits.check-in', $visit) }}">
                                            @csrf
                                            <button class="rounded-lg bg-bank-blue px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700">Check-in</button>
                                        </form>
                                        <form method="POST" action="{{ route('visits.wait', $visit) }}" onsubmit="return confirm('Minta tamu menunggu karena orang yang dituju tidak ada / sedang cuti?')">
                                            @csrf
                                            <button class="rounded-lg bg-orange-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-orange-600">Suruh Tunggu</button>
                                        </form>
                                        <form method="POST" action="{{ route('visits.reject', $visit) }}" onsubmit="return confirm('Tolak kunjungan ini?')">
                                            @csrf
                                            <button class="rounded-lg bg-bank-red/10 px-3 py-1.5 text-xs font-medium text-bank-red hover:bg-bank-red/20">Tolak</button>
                                        </form>
                                    @elseif($visit->status === 'waiting')
                                        <form method="POST" action="{{ route('visits.check-in', $visit) }}">
                                            @csrf
                                            <button class="rounded-lg bg-bank-blue px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700">Check-in</button>
                                        </form>
                                        <form method="POST" action="{{ route('visits.reject', $visit) }}" onsubmit="return confirm('Tolak kunjungan ini?')">
                                            @csrf
                                            <button class="rounded-lg bg-bank-red/10 px-3 py-1.5 text-xs font-medium text-bank-red hover:bg-bank-red/20">Tolak</button>
                                        </form>
                                    @elseif($visit->status === 'active')
                                        <form method="POST" action="{{ route('visits.check-out', $visit) }}" onsubmit="return confirm('Lakukan check-out untuk tamu ini?')">
                                            @csrf
                                            <button class="rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-sky-700">Check-out</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('badge', $visit->qr_code_token) }}" target="_blank"
                                       class="rounded-lg bg-bank-light px-3 py-1.5 text-xs font-medium text-bank-navy hover:bg-bank-light/70">Badge QR</a>
                                    @if($visit->visitor_phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $visit->visitor_phone) }}?text={{ rawurlencode('Salam, kami dari PT Pelindo Regional 1 Dumai. Terkait kunjungan Anda ke ' . ($visit->employee->name ?? 'staff kami') . ': ' . ($visit->status_note ?: 'status kunjungan Anda saat ini adalah ' . ($statusLabel[$visit->status] ?? $visit->status) . '.')) }}"
                                           target="_blank"
                                           class="rounded-lg bg-bank-green/10 px-3 py-1.5 text-xs font-medium text-emerald-600 hover:bg-bank-green/20">WA</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-bank-gray">
                                Belum ada tamu hari ini. Bagikan tautan Kiosk ke tamu untuk mendaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($todayVisits->hasPages())
            <div class="px-5 py-3 border-t border-bank-light">{{ $todayVisits->links() }}</div>
        @endif
    </div>
@endsection
