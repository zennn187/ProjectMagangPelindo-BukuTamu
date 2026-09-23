@extends('layouts.operator')

@section('page_title', 'Kedatangan Tamu (Gate)')

@push('header_actions')
    <a href="{{ route('gate.scanner') }}" class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 transition-colors">
        Buka Scanner
    </a>
@endpush

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <p class="text-sm text-bank-gray">Tamu yang masuk melalui security gate — terupdate otomatis setiap 10 detik.</p>
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <input type="date" name="day" value="{{ $selectedDay }}" class="rounded-xl border border-bank-light bg-white px-3.5 py-2 text-sm outline-none focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / plat / instansi…" class="rounded-xl border border-bank-light bg-white px-3.5 py-2 text-sm outline-none focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 w-48">
            <button type="submit" class="rounded-xl bg-bank-blue px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Filter</button>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
        <div class="bg-white rounded-3xl p-5 shadow-sm ring-1 ring-bank-light/70">
            <div class="flex items-center justify-center h-11 w-11 rounded-full bg-blue-50 text-blue-600 text-lg">👥</div>
            <p class="mt-4 text-sm font-medium text-bank-gray">Total Tamu</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy">{{ $counts['total'] }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm ring-1 ring-bank-light/70">
            <div class="flex items-center justify-center h-11 w-11 rounded-full bg-sky-50 text-sky-600 text-lg">🧍</div>
            <p class="mt-4 text-sm font-medium text-bank-gray">Sedang Di Dalam</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy" id="stat-inside">{{ $counts['inside'] }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm ring-1 ring-bank-light/70">
            <div class="flex items-center justify-center h-11 w-11 rounded-full bg-emerald-50 text-emerald-600 text-lg">✅</div>
            <p class="mt-4 text-sm font-medium text-bank-gray">Sudah Keluar</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy" id="stat-completed">{{ $counts['completed'] }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm ring-1 ring-bank-light/70">
            <div class="flex items-center justify-center h-11 w-11 rounded-full bg-amber-50 text-amber-600 text-lg">⏱</div>
            <p class="mt-4 text-sm font-medium text-bank-gray">Rata-rata Durasi</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy">{{ $counts['avg_duration'] }}</p>
        </div>
    </div>

    {{-- Notifikasi real-time --}}
    <div id="notif-area" class="space-y-2 mb-6"></div>

    {{-- Tabel laporan --}}
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm ring-1 ring-bank-light/70 overflow-x-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-bank-navy">Laporan Kedatangan · {{ \Carbon\Carbon::parse($selectedDay)->translatedFormat('d F Y') }}</h2>
            <span class="text-xs text-bank-gray">Auto-refresh: <span id="poll-status">aktif</span></span>
        </div>
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="text-xs uppercase tracking-wide text-bank-gray border-b border-bank-light">
                    <th class="py-3 pr-4 font-medium">Tamu</th>
                    <th class="py-3 pr-4 font-medium">Identitas</th>
                    <th class="py-3 pr-4 font-medium">Plat</th>
                    <th class="py-3 pr-4 font-medium">Instansi</th>
                    <th class="py-3 pr-4 font-medium">Menemui</th>
                    <th class="py-3 pr-4 font-medium">Masuk</th>
                    <th class="py-3 pr-4 font-medium">Keluar</th>
                    <th class="py-3 pr-4 font-medium">Durasi</th>
                    <th class="py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody id="report-rows">
                @forelse($visits as $visit)
                    <tr class="border-b border-bank-light/60">
                        <td class="py-3 pr-4 font-semibold text-bank-navy whitespace-nowrap">{{ $visit->visitor_name }}</td>
                        <td class="py-3 pr-4 text-bank-gray">{{ $visit->identity_number ?? '-' }}</td>
                        <td class="py-3 pr-4 text-bank-gray">{{ $visit->plate_number ?? '-' }}</td>
                        <td class="py-3 pr-4 text-bank-gray">{{ $visit->visitor_institution }}</td>
                        <td class="py-3 pr-4 text-bank-gray">{{ $visit->person_to_meet ?? '-' }}</td>
                        <td class="py-3 pr-4 text-bank-gray whitespace-nowrap">{{ $visit->check_in_time?->format('H:i') }}</td>
                        <td class="py-3 pr-4 text-bank-gray whitespace-nowrap" data-cell="check_out">{{ $visit->check_out_time?->format('H:i') ?? '—' }}</td>
                        <td class="py-3 pr-4 text-bank-gray whitespace-nowrap" data-cell="duration">{{ $visit->durationText() ?? '—' }}</td>
                        <td class="py-3" data-cell="status">
                            <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $visit->status === \App\Models\Visit::STATUS_COMPLETED ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">
                                {{ $visit->status === \App\Models\Visit::STATUS_COMPLETED ? 'Selesai' : 'Di Dalam' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="py-10 text-center text-bank-gray">Belum ada tamu yang terdaftar pada tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $visits->links() }}</div>
    </div>
@endsection

@push('scripts')
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const notifArea = document.getElementById('notif-area');
        let lastPoll = new Date(Date.now() - 5 * 60000).toISOString();
        let seenEvents = new Set();

        function pushNotif(evt) {
            const isOut = evt.direction === 'out';
            const el = document.createElement('div');
            el.className = 'flex items-start gap-3 rounded-2xl p-4 shadow-sm ring-1 ' +
                (isOut ? 'bg-emerald-50 ring-emerald-200' : 'bg-sky-50 ring-sky-200');
            el.innerHTML = `
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white text-lg ring-1 ring-black/5">${isOut ? '🚪' : '🙋'}</span>
                <div class="flex-1">
                    <p class="text-sm font-semibold ${isOut ? 'text-emerald-800' : 'text-sky-800'}">${evt.message}</p>
                    ${evt.duration ? `<p class="text-xs mt-0.5 ${isOut ? 'text-emerald-600' : 'text-sky-600'}">Durasi kunjungan: ${evt.duration}</p>` : ''}
                </div>
                <span class="text-xs whitespace-nowrap text-bank-gray">${evt.time}</span>`;
            notifArea.prepend(el);
            while (notifArea.children.length > 3) notifArea.lastChild.remove();
            setTimeout(() => el.remove(), 30000);
        }

        function updateRow(v) {
            const row = document.querySelector(`#report-rows tr[data-visit-id="${v.id}"]`);
            if (!row) return;
            const outCell = row.querySelector('[data-cell="check_out"]');
            const durCell = row.querySelector('[data-cell="duration"]');
            const statusCell = row.querySelector('[data-cell="status"]');
            if (v.check_out && outCell) outCell.textContent = v.check_out;
            if (v.duration && durCell) durCell.textContent = v.duration;
            if (v.check_out && statusCell) {
                statusCell.innerHTML = '<span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-semibold bg-emerald-100 text-emerald-700">Selesai</span>';
            }
        }

        async function poll() {
            try {
                const res = await fetch(`{{ route('gate-reports.latest') }}?since=${encodeURIComponent(lastPoll)}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                });
                if (!res.ok) throw new Error('poll failed');
                const data = await res.json();
                lastPoll = data.last_updated_at;

                const insideEl = document.getElementById('stat-inside');
                const completedEl = document.getElementById('stat-completed');
                if (insideEl) insideEl.textContent = data.counts.inside;
                if (completedEl) completedEl.textContent = data.counts.completed;

                data.events.forEach(evt => {
                    if (seenEvents.has(evt.id + evt.direction)) return;
                    seenEvents.add(evt.id + evt.direction);
                    pushNotif(evt);
                });

                data.visits.forEach(updateRow);
            } catch (e) {
                const s = document.getElementById('poll-status');
                if (s) s.textContent = 'menunggu…';
            }
        }

        poll();
        setInterval(poll, 10000);
    </script>

@endpush

