@extends('layouts.operator')

@section('page_title', 'Antrean Gate')

@push('header_actions')
    <a href="{{ route('gate.scanner') }}" class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 transition-colors">
        Buka Scan Gate
    </a>
@endpush

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <p class="text-sm text-bank-gray">Tamu yang masuk melalui security gate — terupdate otomatis setiap 10 detik.</p>
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <input type="date" name="day" value="{{ $selectedDay }}" class="rounded-xl border border-bank-light bg-white px-3.5 py-2 text-sm outline-none focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / NIK / instansi…" class="rounded-xl border border-bank-light bg-white px-3.5 py-2 text-sm outline-none focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 w-48">
            <button type="submit" class="rounded-xl bg-bank-blue px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Filter</button>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
        <div class="bg-white rounded-3xl p-5 shadow-sm ring-1 ring-bank-light/70">
            <div class="mb-4 h-1 w-8 rounded-full bg-bank-blue"></div>
            <p class="text-sm font-medium text-bank-gray">Total Tamu</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy" id="stat-total">{{ $counts['total'] }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm ring-1 ring-bank-light/70">
            <div class="mb-4 h-1 w-8 rounded-full bg-bank-blue"></div>
            <p class="text-sm font-medium text-bank-gray">Sedang Di Dalam</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy" id="stat-inside">{{ $counts['inside'] }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm ring-1 ring-bank-light/70">
            <div class="mb-4 h-1 w-8 rounded-full bg-bank-blue"></div>
            <p class="text-sm font-medium text-bank-gray">Sudah Keluar</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy" id="stat-completed">{{ $counts['completed'] }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm ring-1 ring-bank-light/70">
            <div class="mb-4 h-1 w-8 rounded-full bg-bank-blue"></div>
            <p class="text-sm font-medium text-bank-gray">Rata-rata Durasi</p>
            <p class="mt-1 text-2xl font-bold text-bank-navy">{{ $counts['avg_duration'] }}</p>
        </div>
    </div>

    {{-- Tabel laporan --}}
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm ring-1 ring-bank-light/70 overflow-x-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-bank-navy">Laporan Kedatangan · {{ \Carbon\Carbon::parse($selectedDay)->translatedFormat('d F Y') }}</h2>
            <span class="text-xs text-bank-gray">Auto-refresh: <span id="poll-status">aktif</span></span>
        </div>
        <table class="w-full min-w-[1120px] text-sm text-left">
            <thead>
                <tr class="text-xs uppercase tracking-wide text-bank-gray border-b border-bank-light">
                    <th class="py-3 pr-4 font-medium">Tamu</th>
                    <th class="py-3 pr-4 font-medium">NIK</th>
                    <th class="py-3 pr-4 font-medium">Telepon</th>
                    <th class="py-3 pr-4 font-medium">Instansi</th>
                    <th class="py-3 pr-4 font-medium">Menemui</th>
                    <th class="py-3 pr-4 font-medium">Masuk</th>
                    <th class="py-3 pr-4 font-medium">Keluar</th>
                    <th class="py-3 pr-4 font-medium">Durasi</th>
                    <th class="py-3 pr-4 font-medium">Status</th>
                    <th class="py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody id="report-rows">
                @forelse($visits as $visit)
                    <tr class="border-b border-bank-light/60">
                        @php
                            $reportPhotoUrl = ($visit->photo_exists || ($visit->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($visit->photo_path)))
                                ? route('visits.photo', ['visit' => $visit])
                                : null;
                        @endphp
                        <td class="py-3 pr-4">
                            <div class="flex items-center gap-3">
                                @if($reportPhotoUrl)
                                    <a href="{{ $reportPhotoUrl }}" target="_blank" rel="noopener" class="shrink-0">
                                        <img src="{{ $reportPhotoUrl }}" alt="Foto {{ $visit->visitor_name }}" loading="lazy" class="h-10 w-10 rounded-xl object-cover ring-1 ring-bank-light">
                                    </a>
                                @else
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bank-bg text-xs text-bank-gray">—</span>
                                @endif
                                <span class="font-semibold text-bank-navy whitespace-nowrap">{{ $visit->visitor_name }}</span>
                            </div>
                        </td>
                        <td class="py-3 pr-4 text-bank-gray">{{ $visit->identity_number ?? '-' }}</td>
                        <td class="py-3 pr-4 whitespace-nowrap">
                            @if($visit->visitor_phone)
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $visit->visitor_phone) }}" class="text-bank-gray hover:text-bank-blue">{{ $visit->visitor_phone }}</a>
                            @else
                                <span class="text-bank-gray">—</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-bank-gray">{{ $visit->visitor_institution }}</td>
                        <td class="py-3 pr-4 text-bank-gray">{{ $visit->person_to_meet ?? '-' }}</td>
                        <td class="py-3 pr-4 text-bank-gray whitespace-nowrap">{{ $visit->check_in_time?->format('H:i') }}</td>
                        <td class="py-3 pr-4 text-bank-gray whitespace-nowrap" data-cell="check_out">{{ $visit->check_out_time?->format('H:i') ?? '—' }}</td>
                        <td class="py-3 pr-4 text-bank-gray whitespace-nowrap" data-cell="duration">{{ $visit->durationText() ?? '—' }}</td>
                        <td class="py-3" data-cell="status">
                            <span class="inline-block rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200">
                                {{ $visit->status === \App\Models\Visit::STATUS_COMPLETED ? 'Selesai' : 'Di Dalam' }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if($visit->visitor_phone)
                                <a href="{{ route('gate.chat', $visit) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200 hover:bg-emerald-100 transition-colors whitespace-nowrap">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    Chat WA
                                </a>
                            @else
                                <span class="text-[11px] text-bank-gray">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="py-10 text-center text-bank-gray">Belum ada tamu yang terdaftar pada tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $visits->links() }}</div>
    </div>
@endsection

@push('scripts')
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        let lastPoll = @json(now()->startOfSecond()->toISOString());
        let seenEvents = new Set();

        function pushNotif(evt) {
            const isOut = evt.direction === 'out';
            if (typeof window.showToast !== 'function') {
                console.error('Toast helper tidak tersedia.');
                return;
            }

            const details = [evt.message, evt.duration ? `Durasi ${evt.duration}` : null, evt.time]
                .filter(Boolean)
                .join(' · ');
            window.showToast({
                title: isOut ? 'Kunjungan selesai' : 'Tamu masuk',
                description: details,
                variant: 'success',
                duration: 4200,
            });
        }

        function updateRow(v) {
            const row = document.createElement('tr');
            row.className = 'border-b border-bank-light/60';

            const visitorCell = document.createElement('td');
            visitorCell.className = 'py-3 pr-4';
            const visitor = document.createElement('div');
            visitor.className = 'flex items-center gap-3';
            if (v.photo_url) {
                const photoLink = document.createElement('a');
                photoLink.href = v.photo_url;
                photoLink.target = '_blank';
                photoLink.rel = 'noopener';
                photoLink.className = 'shrink-0';
                const photo = document.createElement('img');
                photo.src = v.photo_url;
                photo.alt = `Foto ${v.name}`;
                photo.loading = 'lazy';
                photo.className = 'h-10 w-10 rounded-xl object-cover ring-1 ring-bank-light';
                photoLink.append(photo);
                visitor.append(photoLink);
            } else {
                const placeholder = document.createElement('span');
                placeholder.className = 'grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bank-bg text-xs text-bank-gray';
                placeholder.textContent = '—';
                visitor.append(placeholder);
            }
            const visitorName = document.createElement('span');
            visitorName.className = 'font-semibold text-bank-navy whitespace-nowrap';
            visitorName.textContent = v.name || '—';
            visitor.append(visitorName);
            visitorCell.append(visitor);
            row.append(visitorCell);

            const addCell = (value, className, fallback = '—') => {
                const cell = document.createElement('td');
                cell.className = className;
                cell.textContent = value || fallback;
                row.append(cell);
                return cell;
            };

            addCell(v.identity, 'py-3 pr-4 text-bank-gray');
            const phoneCell = document.createElement('td');
            phoneCell.className = 'py-3 pr-4 whitespace-nowrap';
            if (v.phone) {
                const phoneLink = document.createElement('a');
                phoneLink.href = `tel:${String(v.phone).replace(/[^0-9+]/g, '')}`;
                phoneLink.className = 'text-bank-gray hover:text-bank-blue';
                phoneLink.textContent = v.phone;
                phoneCell.append(phoneLink);
            } else {
                phoneCell.textContent = '—';
                phoneCell.className += ' text-bank-gray';
            }
            row.append(phoneCell);
            addCell(v.institution, 'py-3 pr-4 text-bank-gray');
            addCell(v.person_to_meet, 'py-3 pr-4 text-bank-gray');
            addCell(v.check_in, 'py-3 pr-4 text-bank-gray whitespace-nowrap');
            addCell(v.check_out, 'py-3 pr-4 text-bank-gray whitespace-nowrap');
            addCell(v.duration, 'py-3 pr-4 text-bank-gray whitespace-nowrap');

            const statusCell = document.createElement('td');
            statusCell.className = 'py-3';
            const status = document.createElement('span');
            const completed = v.status === 'completed';
            status.className = 'inline-block rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200';
            status.textContent = completed ? 'Selesai' : 'Di Dalam';
            statusCell.append(status);
            row.append(statusCell);

            const actionCell = document.createElement('td');
            actionCell.className = 'py-3';
            if (v.chat_url) {
                const link = document.createElement('a');
                link.href = v.chat_url;
                link.className = 'inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200 hover:bg-emerald-100 transition-colors whitespace-nowrap';
                link.textContent = 'Chat WA';
                actionCell.append(link);
            } else {
                actionCell.textContent = '—';
                actionCell.className += ' text-[11px] text-bank-gray';
            }
            row.append(actionCell);
            return row;
        }

        function renderVisits(visits) {
            const tbody = document.getElementById('report-rows');
            if (!visits.length) {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 10;
                cell.className = 'py-10 text-center text-bank-gray';
                cell.textContent = 'Belum ada tamu yang terdaftar pada tanggal ini.';
                row.append(cell);
                tbody.replaceChildren(row);
                return;
            }

            tbody.replaceChildren(...visits.map(updateRow));
        }

        async function poll() {
            if (poll.running) return;
            poll.running = true;
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 5000);
                const params = new URLSearchParams(window.location.search);
                params.set('since', lastPoll);
                const res = await fetch(`{{ route('gate-reports.latest') }}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    signal: controller.signal,
                });
                clearTimeout(timeoutId);
                if (!res.ok) throw new Error('poll failed');
                const data = await res.json();
                lastPoll = data.last_updated_at;

                const totalEl = document.getElementById('stat-total');
                const insideEl = document.getElementById('stat-inside');
                const completedEl = document.getElementById('stat-completed');
                if (totalEl) totalEl.textContent = data.counts.total;
                if (insideEl) insideEl.textContent = data.counts.inside;
                if (completedEl) completedEl.textContent = data.counts.completed;

                data.events.forEach(evt => {
                    if (seenEvents.has(evt.id + evt.direction)) return;
                    seenEvents.add(evt.id + evt.direction);
                    pushNotif(evt);
                });

                renderVisits(data.visits);
                const pollStatus = document.getElementById('poll-status');
                if (pollStatus) pollStatus.textContent = 'aktif';
            } catch (e) {
                if (e?.name === 'AbortError') { poll.running = false; return; }
                const s = document.getElementById('poll-status');
                if (s) s.textContent = 'menunggu…';
            } finally {
                poll.running = false;
            }
        }

        poll();
        setInterval(() => {
            // Hemat resource: pause polling saat tab tidak terlihat.
            if (document.visibilityState === 'visible') poll();
        }, 10000);
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') poll();
        });
    </script>

@endpush

