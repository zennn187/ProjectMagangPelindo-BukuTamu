@extends('layouts.operator')

@section('page_title', 'Dashboard')

@push('header_actions')
    <a href="{{ route('kiosk') }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 transition-colors">
        Buka Kiosk
    </a>
@endpush

@section('content')
    @php
        $statusColor = [
            'pending' => 'bg-amber-100 text-amber-700',
            'waiting' => 'bg-orange-100 text-orange-700',
            'active' => 'bg-sky-100 text-sky-700',
            'completed' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-red-100 text-red-700',
        ];
        $statusLabel = [
            'pending' => 'Belum Verifikasi',
            'waiting' => 'Disuruh Menunggu',
            'active' => 'Di Dalam',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
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
            ['label' => 'Di Dalam', 'value' => $counts['active'], 'icon' => '🧍', 'ring' => 'bg-sky-50 text-sky-600'],
            ['label' => 'Selesai', 'value' => $counts['completed'], 'icon' => '✅', 'ring' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Ditolak', 'value' => $counts['rejected'], 'icon' => '🚫', 'ring' => 'bg-red-50 text-red-500'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <p class="text-sm text-bank-gray">Pantau tamu secara real-time dan lakukan verifikasi.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 2xl:grid-cols-5 gap-4 sm:gap-5 mb-6">
        @foreach($cards as $card)
            <div class="bg-white rounded-3xl p-5 shadow-sm ring-1 ring-bank-light/70 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-center h-11 w-11 rounded-full {{ $card['ring'] }} text-lg">{{ $card['icon'] }}</div>
                <p class="mt-4 text-sm font-medium text-bank-gray truncate">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-bank-navy">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

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
                            <div class="w-full rounded-xl bg-gradient-to-t from-bank-blue to-indigo-400 transition-all" style="height: {{ max(4, round($day['total'] / $maxWeekly * 100)) }}%"></div>
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
                        <div class="h-10 w-10 shrink-0 rounded-full grid place-items-center font-semibold uppercase {{ ['pending' => 'bg-amber-100 text-amber-600', 'waiting' => 'bg-orange-100 text-orange-600', 'active' => 'bg-emerald-100 text-emerald-600', 'completed' => 'bg-emerald-100 text-emerald-600', 'rejected' => 'bg-red-100 text-red-500'][$visit->status] ?? 'bg-bank-light text-bank-blue' }}">
                            {{ strtoupper(substr($visit->visitor_name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-bank-navy truncate">{{ $visit->visitor_name }}</p>
                            <p class="text-xs text-bank-gray truncate">ke {{ $visit->employee->name ?? '—' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs text-bank-gray">{{ $visit->created_at->format('H:i') }}</p>
                            <span class="inline-block h-2 w-2 rounded-full mt-1 {{ ['pending' => 'bg-amber-400', 'waiting' => 'bg-orange-400', 'active' => 'bg-emerald-400', 'completed' => 'bg-emerald-400', 'rejected' => 'bg-red-400'][$visit->status] ?? 'bg-bank-gray' }}"></span>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-bank-gray py-6 text-center">Belum ada kunjungan.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-3xl overflow-hidden shadow-sm ring-1 ring-bank-light/70">
        <div class="px-5 sm:px-6 py-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-bank-navy">Daftar Kunjungan</h2>
            <span class="text-xs text-bank-gray">{{ $visits->total() }} total</span>
        </div>

        <div class="px-5 sm:px-6 pb-5">
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium uppercase tracking-wide text-bank-gray mb-1">Cari</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bank-gray" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, instansi, atau pegawai..." class="soft-search pl-9">
                    </div>
                </div>
                <div class="w-full lg:w-56">
                    <label class="block text-xs font-medium uppercase tracking-wide text-bank-gray mb-1">Tanggal</label>
                    <input type="date" name="day" value="{{ $selectedDay }}" class="soft-date">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-xl bg-bank-blue px-4 py-2.5 text-sm font-medium text-white transition-all duration-200 hover:-translate-y-0.5 hover:bg-indigo-700">Filter</button>
                    <a href="{{ route('dashboard') }}" class="rounded-xl bg-bank-light px-4 py-2.5 text-sm font-medium text-bank-navy transition-all duration-200 hover:-translate-y-0.5 hover:bg-bank-light/80">Reset</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-bank-light text-sm">
                <thead class="bg-bank-bg text-left text-xs uppercase text-bank-gray">
                    <tr>
                        <th class="px-4 py-3 w-12">No</th>
                        <th class="px-4 sm:px-5 py-3">Tamu</th>
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Jam</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 hidden md:table-cell">Keperluan / Catatan</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bank-light">
                    @forelse($visits as $index => $visit)
                        <tr class="hover:bg-bank-bg/60 align-top">
                            <td class="px-4 py-3 text-bank-navy text-center font-medium">{{ $visits->firstItem() + $index }}</td>
                            <td class="px-4 sm:px-5 py-3">
                                <div class="font-medium text-bank-navy">{{ $visit->visitor_name }}</div>
                                <div class="text-xs text-bank-gray">{{ $visit->visitor_institution }}</div>
                                <div class="text-xs text-bank-gray/80">{{ $visit->visitor_phone }}</div>
                                <div class="text-xs text-bank-gray mt-1">ke: <span class="font-medium">{{ $visit->employee->name ?? '—' }}</span></div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $photoUrl = $visit->photo_path && Storage::disk('public')->exists($visit->photo_path)
                                        ? route('visits.photo', ['visit' => $visit], false)
                                        : null;
                                @endphp
                                @if($photoUrl)
                                    <button type="button" data-photo="{{ $photoUrl }}" class="photo-preview inline-flex h-12 w-12 items-center justify-center overflow-hidden rounded-lg border border-bank-light bg-bank-light/30 shadow-sm hover:shadow-md transition" aria-label="Lihat foto tamu">
                                        <img src="{{ $photoUrl }}" alt="Foto tamu" class="h-full w-full object-cover">
                                    </button>
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg border border-dashed border-bank-light bg-bank-bg text-bank-gray text-xs">Foto</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-bank-navy">
                                <div>{{ $typeLabel[$visit->visit_type] ?? 'Kunjungan' }}</div>
                                @if($visit->visit_type === 'deliver')
                                    <div class="text-[11px] text-bank-gray mt-0.5">{{ $prefLabel[$visit->delivery_pref] ?? '' }}</div>
                                    @if($visit->delivery_pref === 'leave' && $visit->received_by_name)
                                        <div class="text-[11px] text-emerald-600 font-medium mt-0.5">Diterima oleh: {{ $visit->received_by_name }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-bank-navy hidden sm:table-cell whitespace-nowrap">{{ $visit->created_at->format('H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColor[$visit->status] ?? 'bg-bank-light text-bank-navy' }}">{{ $statusLabel[$visit->status] ?? $visit->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-bank-navy hidden md:table-cell max-w-[240px]">
                                <div class="truncate" title="{{ $visit->purpose }}">{{ $visit->purpose }}</div>
                                @if($visit->status_note)
                                    <div class="text-[11px] text-amber-600 mt-1" title="{{ $visit->status_note }}">✎ {{ $visit->status_note }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-center gap-1.5">
                                    @if($visit->status === 'pending')
                                        <form method="POST" action="{{ route('visits.check-in', $visit) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-bank-blue px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700">Check-in</button>
                                        </form>
                                        <form method="POST" action="{{ route('visits.wait', $visit) }}" data-confirm="Minta tamu menunggu karena orang yang dituju tidak ada atau sedang cuti?" data-confirm-title="Suruh menunggu" data-confirm-action="Ya, lanjutkan" data-confirm-variant="warning">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-orange-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-orange-600">Suruh Tunggu</button>
                                        </form>
                                        <form method="POST" action="{{ route('visits.reject', $visit) }}" data-confirm="Tolak kunjungan ini? Status tamu akan berubah menjadi ditolak." data-confirm-title="Tolak kunjungan" data-confirm-action="Ya, tolak" data-confirm-variant="danger">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-200">Tolak</button>
                                        </form>
                                    @elseif($visit->status === 'waiting')
                                        <form method="POST" action="{{ route('visits.check-in', $visit) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-bank-blue px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700">Check-in</button>
                                        </form>
                                        <form method="POST" action="{{ route('visits.reject', $visit) }}" data-confirm="Tolak kunjungan ini?" data-confirm-title="Tolak kunjungan" data-confirm-action="Ya, tolak" data-confirm-variant="danger">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-200">Tolak</button>
                                        </form>
                                    @elseif($visit->status === 'active')
                                        <form method="POST" action="{{ route('visits.check-out', $visit) }}" data-confirm="Lakukan check-out untuk tamu ini?" data-confirm-title="Check-out" data-confirm-action="Ya, check-out" data-confirm-variant="success">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">Check-out</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('badge', $visit->qr_code_token) }}" target="_blank" class="rounded-lg bg-bank-light px-3 py-1.5 text-xs font-medium text-bank-navy hover:bg-bank-light/70">Badge QR</a>
                                    @if($visit->visitor_phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $visit->visitor_phone) }}?text={{ rawurlencode('Salam, kami dari PT Pelindo Regional 1 Dumai. Terkait kunjungan Anda ke ' . ($visit->employee->name ?? 'staff kami') . ': ' . ($visit->status_note ?: 'status kunjungan Anda saat ini adalah ' . ($statusLabel[$visit->status] ?? $visit->status) . '.')) }}" target="_blank" class="rounded-lg bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">WA</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-bank-gray">Belum ada tamu pada tanggal yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visits->hasPages())
            <div class="px-5 py-3 border-t border-bank-light">
                {{ $visits->links() }}
            </div>
        @endif
    </div>

    <div id="photo-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/75 p-4 backdrop-blur-md">
        <div class="photo-modal-card relative w-full max-w-4xl rounded-[24px] border border-white/20 bg-white/10 p-2 shadow-[0_30px_90px_rgba(2,6,23,0.55)] backdrop-blur-xl">
            <button type="button" class="glass-modal-close inline-flex h-10 w-10 items-center justify-center rounded-full text-xl font-light text-white transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-white/30" id="close-photo-modal" aria-label="Tutup foto">✕</button>
            <div class="photo-modal-frame overflow-hidden rounded-[18px] border border-white/25 bg-slate-950/30 p-1 shadow-inner">
                <img id="photo-modal-image" src="" alt="Foto tamu besar" class="block max-h-[78vh] w-full rounded-[14px] object-contain bg-slate-100">
            </div>
        </div>
    </div>

    <div id="detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/75 p-4 backdrop-blur-md">
        <div class="detail-modal-card relative w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-[0_24px_70px_rgba(2,6,23,0.35)]">
            <button type="button" class="detail-modal-close inline-flex h-9 w-9 items-center justify-center rounded-full text-xl leading-none text-white transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-white/30" id="close-detail-modal" aria-label="Tutup detail tamu">✕</button>
            <div class="detail-modal-body overflow-y-auto rounded-2xl">
                <div class="detail-modal-grid grid overflow-hidden rounded-2xl">
                <div class="bg-gradient-to-br from-bank-navy to-slate-700 p-4">
                    <div class="detail-modal-photo flex h-32 items-center justify-center overflow-hidden rounded-xl border border-white/20 bg-white/10 shadow-inner">
                        <img id="detail-modal-image" src="" alt="Foto tamu detail" class="hidden h-full w-full object-cover">
                        <span id="detail-modal-placeholder" class="text-center text-xs font-medium uppercase tracking-[0.16em] text-white/60">Foto<br>tamu</span>
                    </div>
                    <p class="mt-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/60">Data kunjungan</p>
                    <p id="detail-visit-type" class="mt-1 text-sm font-medium text-white">—</p>
                </div>

                <div class="min-w-0 p-4 sm:p-5">
                    <div class="border-b border-bank-light pb-4 pr-8">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-bank-blue">Detail Tamu</p>
                        <h3 id="detail-visitor-name" class="mt-1 text-xl font-bold tracking-tight text-bank-navy">—</h3>
                        <p id="detail-employee" class="mt-1 text-sm text-bank-gray">Bertemu dengan —</p>
                    </div>

                    <div class="mt-4 grid gap-x-5 gap-y-3 sm:grid-cols-2">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-bank-gray">Instansi</p>
                            <p id="detail-institution" class="mt-1 text-sm font-medium text-bank-navy">—</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-bank-gray">Telepon</p>
                            <p id="detail-phone" class="mt-1 text-sm font-medium text-bank-navy">—</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-bank-gray">Keperluan</p>
                            <p id="detail-purpose" class="mt-1 text-sm leading-5 text-bank-navy">—</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-bank-gray">Status saat ini</p>
                            <p id="detail-status" class="mt-1 inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">—</p>
                        </div>
                    </div>

                    <div id="detail-actions" class="mt-4 grid grid-cols-3 items-stretch gap-2 border-t border-bank-light pt-3">
                        <form id="detail-check-in-form" method="POST" action="" class="hidden">
                            @csrf
                            <button type="submit" class="flex min-h-10 w-full items-center justify-center rounded-lg bg-bank-blue px-2 py-2.5 text-xs font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-md sm:text-sm">Check-in</button>
                        </form>
                        <form id="detail-wait-form" method="POST" action="" class="hidden" data-confirm="Minta tamu menunggu karena orang yang dituju tidak ada atau sedang cuti?" data-confirm-title="Suruh menunggu" data-confirm-action="Ya, lanjutkan" data-confirm-variant="warning">
                            @csrf
                            <button type="submit" class="flex min-h-10 w-full items-center justify-center rounded-lg bg-orange-500 px-2 py-2.5 text-xs font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-md sm:text-sm">Suruh Tunggu</button>
                        </form>
                        <form id="detail-reject-form" method="POST" action="" class="hidden" data-confirm="Tolak kunjungan ini? Status tamu akan berubah menjadi ditolak." data-confirm-title="Tolak kunjungan" data-confirm-action="Ya, tolak" data-confirm-variant="danger">
                            @csrf
                            <button type="submit" class="flex min-h-10 w-full items-center justify-center rounded-lg border border-red-200 bg-red-50 px-2 py-2.5 text-xs font-bold text-red-600 shadow-sm transition hover:-translate-y-0.5 hover:bg-red-100 hover:shadow-md sm:text-sm">Tolak</button>
                        </form>
                        <form id="detail-check-out-form" method="POST" action="" class="hidden" data-confirm="Lakukan check-out untuk tamu ini?" data-confirm-title="Check-out" data-confirm-action="Ya, check-out" data-confirm-variant="success">
                            @csrf
                            <button type="submit" class="flex min-h-10 w-full items-center justify-center rounded-lg bg-emerald-600 px-2 py-2.5 text-xs font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-emerald-700 hover:shadow-md sm:text-sm">Check-out</button>
                        </form>
                        <span id="detail-no-actions" class="col-span-3 hidden text-xs text-bank-gray">Tidak ada aksi untuk status ini.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .detail-modal-card {
                margin: 0.75rem;
            }

            .detail-modal-body {
                max-height: calc(100vh - 2rem);
            }

            .detail-modal-photo {
                min-height: 8rem;
                max-height: 8rem;
            }

            .detail-modal-grid {
                grid-template-columns: 110px minmax(0, 1fr);
            }

            .detail-modal-close {
                position: absolute;
                top: -0.75rem;
                right: -0.75rem;
                z-index: 10;
                border: 1px solid rgba(255, 255, 255, 0.45);
                background: rgba(30, 41, 59, 0.52);
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.18);
                -webkit-backdrop-filter: blur(14px);
                backdrop-filter: blur(14px);
            }

            .glass-modal-close {
                position: absolute;
                top: -0.75rem;
                right: -0.75rem;
                z-index: 10;
                border: 1px solid rgba(255, 255, 255, 0.45);
                background: rgba(30, 41, 59, 0.52);
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.18);
                -webkit-backdrop-filter: blur(14px);
                backdrop-filter: blur(14px);
            }

            .photo-modal-frame {
                max-height: calc(100vh - 2rem);
            }

            .photo-modal-frame img {
                max-height: calc(100vh - 3rem);
            }

            @media (max-width: 639px) {
                .detail-modal-card {
                    margin: 0.5rem;
                }

                .detail-modal-body {
                    max-height: calc(100vh - 1.5rem);
                }

                .detail-modal-grid {
                    grid-template-columns: 1fr;
                }

                .detail-modal-photo {
                    min-height: 6rem;
                    max-height: 6rem;
                }

                .detail-modal-close {
                    top: -0.6rem;
                    right: -0.6rem;
                }

                .glass-modal-close {
                    top: -0.6rem;
                    right: -0.6rem;
                }
            }

            .visit-row-new {
                background: rgba(16, 185, 129, 0.08);
                box-shadow: inset 0 0 0 1px rgba(16, 185, 129, 0.2);
                transition: all 0.35s ease;
            }

            .live-toast {
                position: fixed;
                right: 1.25rem;
                bottom: 1.25rem;
                z-index: 60;
                min-width: 320px;
                max-width: 380px;
                padding: 0.85rem 1rem;
                border-radius: 1rem;
                background: rgba(15, 23, 42, 0.92);
                color: white;
                box-shadow: 0 20px 50px rgba(15, 23, 42, 0.25);
                border: 1px solid rgba(148, 163, 184, 0.3);
                display: flex;
                gap: 0.75rem;
                align-items: flex-start;
                transform: translateY(18px);
                opacity: 0;
                pointer-events: none;
                transition: all 0.25s ease;
            }

            .live-toast.show {
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
            }

            .live-toast-icon {
                width: 2rem;
                height: 2rem;
                border-radius: 9999px;
                background: rgba(16, 185, 129, 0.2);
                color: #bbf7d0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                flex-shrink: 0;
            }

            .live-toast-title {
                font-size: 0.8rem;
                font-weight: 700;
                margin-bottom: 0.15rem;
            }

            .live-toast-message {
                font-size: 0.75rem;
                line-height: 1.35;
                color: rgba(255, 255, 255, 0.82);
            }

            .live-toast-action {
                display: inline-flex;
                align-items: center;
                margin-top: 0.5rem;
                background: rgba(59, 130, 246, 0.2);
                color: #dbeafe;
                border: 1px solid rgba(96, 165, 250, 0.3);
                border-radius: 0.75rem;
                padding: 0.35rem 0.55rem;
                font-size: 0.7rem;
                font-weight: 600;
                text-decoration: none;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('photo-modal');
                const modalImage = document.getElementById('photo-modal-image');
                const detailModal = document.getElementById('detail-modal');
                const detailModalImage = document.getElementById('detail-modal-image');
                const detailModalPlaceholder = document.getElementById('detail-modal-placeholder');
                const detailVisitorName = document.getElementById('detail-visitor-name');
                const detailInstitution = document.getElementById('detail-institution');
                const detailPhone = document.getElementById('detail-phone');
                const detailPurpose = document.getElementById('detail-purpose');
                const detailStatus = document.getElementById('detail-status');
                const detailEmployee = document.getElementById('detail-employee');
                const detailVisitType = document.getElementById('detail-visit-type');
                const detailCheckInForm = document.getElementById('detail-check-in-form');
                const detailWaitForm = document.getElementById('detail-wait-form');
                const detailRejectForm = document.getElementById('detail-reject-form');
                const detailCheckOutForm = document.getElementById('detail-check-out-form');
                const detailNoActions = document.getElementById('detail-no-actions');
                const closeButton = document.getElementById('close-photo-modal');
                const closeDetailButton = document.getElementById('close-detail-modal');
                const tableBody = document.querySelector('table tbody');
                const latestVisitsMap = new Map();
                const actionToast = @json(session('visit_action'));
                const lastVisitStates = new Map();
                let lastVisitIds = [];
                let hasLoadedVisits = false;
                let actionToastShown = false;

                const toast = document.createElement('div');
                toast.className = 'live-toast';
                toast.innerHTML = `
                    <div class="live-toast-icon">✓</div>
                    <div>
                        <div class="live-toast-title">Tamu Baru Masuk</div>
                        <div class="live-toast-message">Data kunjungan baru telah ditambahkan.</div>
                        <a href="#" class="live-toast-action">Lihat detail</a>
                    </div>
                `;
                document.body.appendChild(toast);

                const openVisitDetail = (visit) => {
                    if (!visit) return;

                    const photo = visit.photo_url || '';
                    detailModalImage.src = photo;
                    detailModalImage.classList.toggle('hidden', !photo);
                    detailModalPlaceholder.classList.toggle('hidden', Boolean(photo));
                    detailVisitorName.textContent = visit.visitor_name || '—';
                    detailInstitution.textContent = visit.visitor_institution || '—';
                    detailPhone.textContent = visit.visitor_phone || '—';
                    detailPurpose.textContent = visit.purpose || '—';
                    detailStatus.textContent = statusLabel[visit.status] || visit.status || '—';
                    detailEmployee.textContent = visit.employee_name || '—';
                    detailVisitType.textContent = typeLabel[visit.visit_type] || 'Kunjungan';

                    [detailCheckInForm, detailWaitForm, detailRejectForm, detailCheckOutForm].forEach((form) => {
                        form.classList.add('hidden');
                        form.action = '';
                    });
                    detailNoActions.classList.add('hidden');

                    if (visit.status === 'pending' || visit.status === 'waiting') {
                        detailCheckInForm.action = `{{ url('/visits') }}/${visit.id}/check-in`;
                        detailRejectForm.action = `{{ url('/visits') }}/${visit.id}/reject`;
                        detailCheckInForm.classList.remove('hidden');
                        detailRejectForm.classList.remove('hidden');
                    }

                    if (visit.status === 'pending') {
                        detailWaitForm.action = `{{ url('/visits') }}/${visit.id}/wait`;
                        detailWaitForm.classList.remove('hidden');
                    }

                    if (visit.status === 'active') {
                        detailCheckOutForm.action = `{{ url('/visits') }}/${visit.id}/check-out`;
                        detailCheckOutForm.classList.remove('hidden');
                    }

                    if (['pending', 'waiting', 'active'].indexOf(visit.status) === -1) {
                        detailNoActions.classList.remove('hidden');
                    }

                    detailModal.classList.remove('hidden');
                    detailModal.classList.add('flex');
                };

                const showToast = (titleText, messageText, visitId = null, photoUrl = '') => {
                    const title = toast.querySelector('.live-toast-title');
                    const message = toast.querySelector('.live-toast-message');
                    const action = toast.querySelector('.live-toast-action');
                    if (title) title.textContent = titleText || 'Pembaruan Kunjungan';
                    if (message) message.textContent = messageText || 'Data kunjungan telah diperbarui.';
                    if (action) {
                        action.href = '#';
                        action.textContent = 'Lihat detail';
                        action.onclick = (event) => {
                            event.preventDefault();

                            const visit = visitId ? latestVisitsMap.get(String(visitId)) : null;

                            if (visitId) {
                                const row = tableBody.querySelector('tr[data-visit-id="' + visitId + '"]');
                                if (row) {
                                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    row.classList.add('visit-row-new');
                                    setTimeout(() => row.classList.remove('visit-row-new'), 4000);
                                }
                            }

                            if (visit) {
                                openVisitDetail(visit);
                                return;
                            }

                            if (photoUrl) {
                                modalImage.src = photoUrl;
                                modal.classList.remove('hidden');
                                modal.classList.add('flex');
                            }
                        };
                    }

                    toast.classList.add('show');
                    clearTimeout(showToast.timeoutId);
                    showToast.timeoutId = setTimeout(() => toast.classList.remove('show'), 5000);
                };

                const notifyVisitEvent = (title = '', message = '', visitId = null, photoUrl = '') => {
                    showToast(title, message, visitId, photoUrl);

                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (!AudioContext) return;

                        const context = new AudioContext();
                        const oscillator = context.createOscillator();
                        const gain = context.createGain();

                        oscillator.type = 'sine';
                        oscillator.frequency.value = 780;
                        gain.gain.value = 0.035;

                        oscillator.connect(gain);
                        gain.connect(context.destination);

                        oscillator.start();
                        oscillator.stop(context.currentTime + 0.12);
                    } catch (error) {
                        console.warn('Browser sound notification tidak tersedia:', error);
                    }
                };

                const showActionToast = () => {
                    if (actionToastShown || !actionToast || !actionToast.visitor_name) return;

                    const actionMessages = {
                        'check-in': ['Check-in Berhasil', `${actionToast.visitor_name} telah melakukan check-in.`],
                        'check-out': ['Check-out Berhasil', `${actionToast.visitor_name} telah melakukan checkout.`],
                        waiting: ['Tamu Menunggu', `${actionToast.visitor_name} diminta untuk menunggu.`],
                        rejected: ['Kunjungan Ditolak', `${actionToast.visitor_name} kunjungannya telah ditolak.`],
                    };
                    const actionMessage = actionMessages[actionToast.type] || ['Status Diperbarui', `${actionToast.visitor_name} status kunjungannya telah diperbarui.`];
                    const visit = actionToast.visit_id ? latestVisitsMap.get(String(actionToast.visit_id)) : null;

                    notifyVisitEvent(actionMessage[0], actionMessage[1], actionToast.visit_id || null, visit ? visit.photo_url : '');
                    actionToastShown = true;
                };

                document.querySelectorAll('.photo-preview').forEach((button) => {
                    button.addEventListener('click', function () {
                        const src = this.dataset.photo;
                        if (!src) return;
                        modalImage.src = src;
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });
                });

                const statusColor = {
                    pending: 'bg-amber-100 text-amber-700',
                    waiting: 'bg-orange-100 text-orange-700',
                    active: 'bg-sky-100 text-sky-700',
                    completed: 'bg-emerald-100 text-emerald-700',
                    rejected: 'bg-red-100 text-red-700',
                };

                const statusLabel = {
                    pending: 'Belum Verifikasi',
                    waiting: 'Disuruh Menunggu',
                    active: 'Di Dalam',
                    completed: 'Selesai',
                    rejected: 'Ditolak',
                };

                const typeLabel = {
                    meet: 'Kunjungan',
                    deliver: 'Pengantaran Surat',
                    meeting_invitation: 'Undangan Rapat / Kegiatan',
                };

                const prefLabel = {
                    hand_in: 'Diantar langsung ke yang dituju',
                    leave: 'Dititipkan ke resepsionis',
                };

                const renderVisitRow = (visit, index) => {
                    const photoCell = visit.photo_url
                        ? `<button type="button" data-photo="${visit.photo_url}" class="photo-preview inline-flex h-12 w-12 items-center justify-center overflow-hidden rounded-lg border border-bank-light bg-bank-light/30 shadow-sm hover:shadow-md transition" aria-label="Lihat foto tamu"><img src="${visit.photo_url}" alt="Foto tamu" class="h-full w-full object-cover"></button>`
                        : `<div class="flex h-12 w-12 items-center justify-center rounded-lg border border-dashed border-bank-light bg-bank-bg text-bank-gray text-xs">Foto</div>`;

                    const typeCell = visit.visit_type === 'deliver'
                        ? `<div>${typeLabel[visit.visit_type] ?? 'Kunjungan'}</div><div class="text-[11px] text-bank-gray mt-0.5">${prefLabel[visit.delivery_pref] ?? ''}</div>${visit.received_by_name ? '<div class="text-[11px] text-emerald-600 font-medium mt-0.5">Diterima oleh: ' + visit.received_by_name + '</div>' : ''}`
                        : `<div>${typeLabel[visit.visit_type] ?? 'Kunjungan'}</div>`;

                    const statusBadge = `<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ${statusColor[visit.status] ?? 'bg-bank-light text-bank-navy'}">${statusLabel[visit.status] ?? visit.status}</span>`;

                    const waDigits = (visit.visitor_phone || '').replace(/[^0-9]/g, '');
                    const waMessage = `Salam, kami dari PT Pelindo Regional 1 Dumai. Terkait kunjungan Anda ke ${visit.employee_name}: ${visit.status_note || 'status kunjungan Anda saat ini adalah ' + (statusLabel[visit.status] ?? visit.status) + '.'}`;
                    const waBadge = waDigits
                        ? `<a href="https://wa.me/${waDigits}?text=${encodeURIComponent(waMessage)}" target="_blank" class="rounded-lg bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">WA</a>`
                        : '';

                    return `
                        <tr data-visit-id="${visit.id}" class="hover:bg-bank-bg/60 align-top">
                            <td class="px-4 py-3 text-bank-navy text-center font-medium">${index}</td>
                            <td class="px-4 sm:px-5 py-3">
                                <div class="font-medium text-bank-navy">${visit.visitor_name}</div>
                                <div class="text-xs text-bank-gray">${visit.visitor_institution}</div>
                                <div class="text-xs text-bank-gray/80">${visit.visitor_phone}</div>
                                <div class="text-xs text-bank-gray mt-1">ke: <span class="font-medium">${visit.employee_name}</span></div>
                            </td>
                            <td class="px-4 py-3">${photoCell}</td>
                            <td class="px-4 py-3 text-bank-navy">${typeCell}</td>
                            <td class="px-4 py-3 text-bank-navy hidden sm:table-cell whitespace-nowrap">${visit.created_at}</td>
                            <td class="px-4 py-3">${statusBadge}</td>
                            <td class="px-4 py-3 text-bank-navy hidden md:table-cell max-w-[240px]">
                                <div class="truncate" title="${visit.purpose}">${visit.purpose}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-center gap-1.5">
                                    <button type="button" data-visit-id="${visit.id}" class="detail-visit-btn rounded-lg bg-sky-100 px-3 py-1.5 text-xs font-medium text-sky-700 hover:bg-sky-200">Detail</button>
                                    <a href="/badge/${visit.qr_code_token || ''}" target="_blank" class="rounded-lg bg-bank-light px-3 py-1.5 text-xs font-medium text-bank-navy hover:bg-bank-light/70">Badge QR</a>
                                    ${waBadge}
                                </div>
                            </td>
                        </tr>
                    `;
                };

                const bindPhotoPreview = () => {
                    document.querySelectorAll('.photo-preview').forEach((button) => {
                        button.addEventListener('click', function () {
                            const src = this.dataset.photo;
                            if (!src) return;
                            modalImage.src = src;
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                        });
                    });
                };

                const bindDetailButtons = () => {
                    document.querySelectorAll('.detail-visit-btn').forEach((button) => {
                        button.addEventListener('click', function () {
                            const visitId = this.dataset.visitId;
                            const visit = latestVisitsMap.get(String(visitId));
                            if (visit) {
                                openVisitDetail(visit);
                            }
                        });
                    });
                };

                const loadLatestVisits = async () => {
                    try {
                        const response = await fetch('{{ route('dashboard.latest') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if (!response.ok) return;

                        const data = await response.json();
                        if (!data.visits || !tableBody) return;

                        const currentIds = data.visits.map((visit) => String(visit.id));
                        const newVisits = data.visits.filter((visit) => !lastVisitIds.includes(String(visit.id)));
                        const changedVisits = data.visits.filter((visit) => {
                            const previousStatus = lastVisitStates.get(String(visit.id));
                            return previousStatus && previousStatus !== visit.status;
                        });

                        latestVisitsMap.clear();
                        data.visits.forEach((visit) => {
                            latestVisitsMap.set(String(visit.id), visit);
                            lastVisitStates.set(String(visit.id), visit.status);
                        });

                        tableBody.innerHTML = data.visits.map((visit, index) => renderVisitRow(visit, index + 1)).join('');
                        bindPhotoPreview();
                        bindDetailButtons();
                        showActionToast();

                        if (hasLoadedVisits && newVisits.length > 0) {
                            const newestVisit = newVisits[0];

                            newVisits.forEach((visit) => {
                                const row = tableBody.querySelector('tr[data-visit-id="' + visit.id + '"]');
                                if (!row) return;

                                row.classList.add('visit-row-new');
                                setTimeout(() => row.classList.remove('visit-row-new'), 6000);
                            });

                            notifyVisitEvent('Tamu Baru Masuk', `${newestVisit.visitor_name} telah mendaftar kunjungan.`, newestVisit.id, newestVisit.photo_url);
                        } else if (hasLoadedVisits && changedVisits.length > 0) {
                            const changedVisit = changedVisits[0];
                            const eventMessages = {
                                active: 'telah melakukan check-in.',
                                completed: 'telah melakukan checkout.',
                                waiting: 'diminta untuk menunggu.',
                                rejected: 'kunjungannya telah ditolak.',
                            };
                            const message = eventMessages[changedVisit.status] || 'status kunjungannya telah diperbarui.';
                            notifyVisitEvent('Status Kunjungan Diperbarui', `${changedVisit.visitor_name} ${message}`, changedVisit.id, changedVisit.photo_url);
                        }

                        lastVisitIds = currentIds;
                        hasLoadedVisits = true;
                    } catch (error) {
                        console.warn('Polling dashboard gagal:', error);
                    }
                };

                const hideModal = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modalImage.src = '';
                };

                closeButton.addEventListener('click', hideModal);
                closeDetailButton.addEventListener('click', function () {
                    detailModal.classList.add('hidden');
                    detailModal.classList.remove('flex');
                });
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        hideModal();
                    }
                });
                detailModal.addEventListener('click', function (event) {
                    if (event.target === detailModal) {
                        detailModal.classList.add('hidden');
                        detailModal.classList.remove('flex');
                    }
                });

                loadLatestVisits();
                setInterval(loadLatestVisits, 5000);
            });
        </script>
    @endpush
@endsection
