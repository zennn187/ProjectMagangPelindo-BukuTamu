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
        <div class="relative w-full max-w-3xl rounded-[28px] border border-white/20 bg-white/10 p-2 shadow-[0_30px_90px_rgba(2,6,23,0.55)] backdrop-blur-xl">
            <button type="button" class="absolute -right-3 -top-3 z-10 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/60 bg-white text-xl font-light text-slate-700 shadow-xl transition hover:scale-105 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-white/30" id="close-photo-modal" aria-label="Tutup foto">✕</button>
            <div class="overflow-hidden rounded-[22px] border border-white/25 bg-slate-950/30 p-1 shadow-inner">
                <img id="photo-modal-image" src="" alt="Foto tamu besar" class="max-h-[80vh] w-full rounded-[18px] object-contain bg-slate-100">
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('photo-modal');
                const modalImage = document.getElementById('photo-modal-image');
                const closeButton = document.getElementById('close-photo-modal');

                document.querySelectorAll('.photo-preview').forEach((button) => {
                    button.addEventListener('click', function () {
                        const src = this.dataset.photo;
                        if (!src) return;
                        modalImage.src = src;
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });
                });

                const hideModal = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modalImage.src = '';
                };

                closeButton.addEventListener('click', hideModal);
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        hideModal();
                    }
                });
            });
        </script>
    @endpush
@endsection
