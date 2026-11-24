@extends('layouts.operator')

@section('page_title', 'Scanner Gate')

@push('header_actions')
    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Live
    </span>
@endpush

@section('content')
    @if(!empty($dbDown))
        <div class="mb-4 rounded-2xl bg-amber-50 p-4 text-sm text-amber-800 ring-1 ring-amber-200" role="alert">
            Database belum terhubung — hasil scan tidak bisa diproses sampai <strong>DB online</strong> diisi di Environment Variables lalu <strong>Redeploy</strong>.
        </div>
    @endif
    <div id="scanner-connection-status" class="mb-4 hidden rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-950" role="status" aria-live="polite"></div>
    <div class="grid lg:grid-cols-5 gap-4 sm:gap-5 mb-6">
        <div class="lg:col-span-3 bg-white rounded-3xl p-5 sm:p-6 shadow-sm ring-1 ring-bank-light/70">
            <h2 class="font-semibold text-bank-navy mb-1">Scan Barcode</h2>
            <p class="text-xs text-bank-gray mb-4">Arahkan kamera ke barcode tamu — barcode <strong>checkout</strong> (sekali pakai) atau <strong>badge masuk</strong>.</p>

            <div id="reader" class="rounded-2xl overflow-hidden ring-1 ring-bank-light bg-black/90" style="min-height:280px;"></div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <button type="button" id="btn-start" class="rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Mulai Kamera</button>
                <button type="button" id="btn-stop" class="hidden rounded-xl bg-white px-5 py-2.5 text-sm font-medium text-bank-navy ring-1 ring-bank-light hover:bg-bank-bg transition-colors">Hentikan</button>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-medium text-bank-gray mb-1.5">Input manual (ketik / tempel token barcode)</label>
                <form id="manual-form" class="flex gap-2">
                    <input type="text" id="manual-code" class="flex-1 rounded-xl border border-bank-light px-4 py-2.5 text-sm outline-none focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20" placeholder="Token barcode…" autocomplete="off">
                    <button type="submit" class="rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Proses</button>
                </form>
            </div>

        </div>

        <div class="lg:col-span-2 space-y-4 sm:space-y-5">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm ring-1 ring-bank-light/70">
                <h2 class="font-semibold text-bank-navy mb-1">Barcode Form Tamu</h2>
                <p class="text-xs text-bank-gray mb-4">Tempel/cetak QR ini di kaca security. Tamu memindainya dengan HP untuk membuka form kedatangan.</p>
                <div class="flex items-center gap-4">
                    <div class="shrink-0 rounded-2xl bg-white p-2 ring-1 ring-bank-light">{!! $glassQrSvg !!}</div>
                    <div class="text-xs text-bank-gray leading-relaxed">
                        <p><span class="font-semibold text-bank-navy">URL:</span></p>
                        <p class="break-all">{{ route('gate.form') }}</p>
                        <button type="button" onclick="window.print()" class="mt-3 rounded-lg bg-bank-blue px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">Cetak QR</button>
                    </div>
                </div>
            </div>

            <div class="scanner-testing-card bg-amber-50 rounded-3xl p-5 sm:p-6 shadow-sm ring-1 ring-amber-200">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="font-semibold text-bank-navy">Testing Scan</h2>
                    <span class="scanner-dev-badge rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-700">Dev only</span>
                </div>
                <p class="text-xs text-bank-gray mb-4">Barcode checkout terakhir — tampilkan QR ini ke kamera HP lain untuk test. Bukan badge QR kiosk.</p>
                @if(!empty($dbDown))
                    <p class="text-xs text-bank-gray">Database belum terhubung — kartu testing aktif lagi setelah DB online.</p>
                @elseif(isset($latestTestVisit) && $latestTestVisit && isset($latestTestQrSvg) && $latestTestQrSvg)
                    <div class="flex items-center gap-4">
                        <div class="shrink-0 rounded-2xl bg-white p-2 ring-1 ring-amber-200">{!! $latestTestQrSvg !!}</div>
                        <div class="text-xs text-bank-gray leading-relaxed min-w-0">
                            <p class="font-semibold text-bank-navy truncate">{{ $latestTestVisit->visitor_name }}</p>
                            <p>Status: <span class="font-semibold {{ $latestTestVisit->checkout_used_at ? 'text-red-600' : 'text-emerald-600' }}">{{ $latestTestVisit->checkout_used_at ? 'Sudah dipakai '.$latestTestVisit->checkout_used_at->format('H:i') : $latestTestVisit->status }}</span></p>
                            <p>Masuk: {{ $latestTestVisit->check_in_time?->format('d/m H:i') ?? '-' }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('gate.pass', $latestTestVisit->checkout_token) }}" target="_blank" class="rounded-lg bg-bank-navy px-3 py-2 text-xs font-semibold text-white hover:bg-bank-blue transition-colors">Lihat barcode terakhir</a>
                                <button type="button" id="btn-fill-test" data-token="{{ $latestTestVisit->checkout_token }}" class="rounded-lg bg-white px-3 py-2 text-xs font-semibold text-bank-navy ring-1 ring-amber-300 hover:bg-amber-100 transition-colors">Isi ke input manual</button>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-bank-gray">Belum ada data tamu gate. Daftarkan 1 tamu dulu via <a href="{{ route('gate.form') }}" target="_blank" class="font-semibold text-bank-blue hover:underline">form gate</a>.</p>
                @endif
            </div>

            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm ring-1 ring-bank-light/70">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="font-semibold text-bank-navy">Aktivitas Hari Ini</h2>
                        <p class="mt-1 text-xs text-bank-gray">{{ \Illuminate\Support\Carbon::parse($activityDay)->translatedFormat('d F Y') }} · <span id="activity-count">{{ $activityCount }}</span> aktivitas</p>
                    </div>
                    <a href="{{ route('gate-reports.index') }}" class="text-xs font-medium text-bank-blue hover:underline">Laporan →</a>
                </div>
                <ul id="recent-scans" class="space-y-3 text-sm text-bank-gray" aria-live="polite">
                    @forelse($recentActivities as $activity)
                        <li data-activity-key="{{ ($activity['visit_id'] ?? '') . ':' . $activity['direction'] }}" class="flex items-start justify-between gap-3 border-b border-bank-light pb-3 last:border-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-bank-navy">{{ $activity['name'] }}</p>
                                <p class="mt-0.5 text-xs text-bank-gray">{{ $activity['label'] }} · {{ $activity['message'] }}</p>
                            </div>
                            <span class="shrink-0 text-xs tabular-nums text-bank-gray">{{ $activity['time'] }}</span>
                        </li>
                    @empty
                        <li class="text-xs text-bank-gray">Belum ada aktivitas hari ini.</li>
                    @endforelse
                </ul>
                <p id="activity-status" class="mt-3 hidden text-xs text-rose-700" role="status"></p>
                <button type="button" id="toggle-activities" class="mt-4 hidden text-xs font-semibold text-bank-blue hover:underline">
                    Lihat semua aktivitas hari ini
                </button>
                <div id="all-activities" class="mt-4 hidden border-t border-bank-light pt-4">
                    <ul id="all-activity-list" class="space-y-3 text-sm text-bank-gray" aria-label="Seluruh aktivitas hari ini"></ul>
                    <button type="button" id="load-more-activities" class="mt-4 hidden rounded-lg border border-bank-light px-3 py-2 text-xs font-semibold text-bank-navy hover:bg-bank-bg">
                        Muat aktivitas lain
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        let html5QrCode = null, scanning = false, lockUntil = 0, scanInProgress = false;
        const recentList = document.getElementById('recent-scans');
        const allActivityPanel = document.getElementById('all-activities');
        const allActivityList = document.getElementById('all-activity-list');
        const activityStatus = document.getElementById('activity-status');
        const activityCountElement = document.getElementById('activity-count');
        const toggleActivitiesButton = document.getElementById('toggle-activities');
        const loadMoreActivitiesButton = document.getElementById('load-more-activities');
        const activityDay = @json($activityDay);
        const activityEndpoint = @json(route('gate.activities'));
        let activitySince = @json($activitySince);
        let activityCount = @json($activityCount);
        let activityPage = 0;
        let activityLastPage = 1;
        let activitiesExpanded = false;
        let activityPollRunning = false;
        const seenActivityKeys = new Set(
            Array.from(recentList.querySelectorAll('[data-activity-key]'))
                .map(item => item.dataset.activityKey)
                .filter(Boolean)
        );

        function activityKey(entry) {
            const visitId = entry.visit_id || entry.id;
            return visitId && entry.direction ? `${visitId}:${entry.direction}` : '';
        }

        function createActivityItem(entry) {
            const item = document.createElement('li');
            item.className = 'flex items-start justify-between gap-3 border-b border-bank-light pb-3 last:border-0 last:pb-0';
            const details = document.createElement('div');
            details.className = 'min-w-0';
            const name = document.createElement('p');
            name.className = 'truncate font-semibold text-bank-navy';
            name.textContent = entry.name || 'Data tamu tidak tersedia';
            const message = document.createElement('p');
            message.className = 'mt-0.5 text-xs text-bank-gray';
            message.textContent = `${entry.label || (entry.direction === 'out' ? 'Checkout' : 'Check-in')} · ${entry.message || (entry.direction === 'out' ? 'Kunjungan selesai' : 'Kedatangan tercatat')}`;
            details.append(name, message);
            const time = document.createElement('span');
            time.className = 'shrink-0 text-xs tabular-nums text-bank-gray';
            time.textContent = entry.time || '-';
            item.append(details, time);

            const key = activityKey(entry);
            if (key) item.dataset.activityKey = key;
            return item;
        }

        function updateActivityCount() {
            activityCountElement.textContent = String(activityCount);
            toggleActivitiesButton.textContent = activitiesExpanded
                ? 'Tampilkan 5 terbaru'
                : `Lihat semua aktivitas hari ini (${activityCount})`;
            toggleActivitiesButton.classList.toggle('hidden', activityCount === 0);
        }

        function showActivityToast(entry) {
            if (typeof window.showToast !== 'function') {
                console.error('Toast helper tidak tersedia.');
                return;
            }

            const isCheckout = entry.direction === 'out';
            window.showToast({
                title: isCheckout ? 'Kunjungan selesai' : 'Tamu masuk',
                description: `${entry.name || 'Tamu'} · ${entry.time || '-'}`,
                variant: 'success',
                duration: 4200,
            });
        }

        function addRecent(entry, notify = false) {
            const key = activityKey(entry);
            if (key && seenActivityKeys.has(key)) return;
            if (key) seenActivityKeys.add(key);

            if (!recentList.querySelector('[data-activity-key]')) recentList.replaceChildren();
            recentList.prepend(createActivityItem(entry));
            while (recentList.children.length > 5) recentList.lastChild.remove();

            activityCount += 1;
            updateActivityCount();
            if (notify) showActivityToast(entry);

            if (activitiesExpanded) {
                const exists = Array.from(allActivityList.children)
                    .some(item => item.dataset.activityKey === key);
                if (!exists) allActivityList.prepend(createActivityItem(entry));
            }
        }

        async function loadActivities(page = 1, append = false) {
            const params = new URLSearchParams({ day: activityDay, page: String(page) });
            const response = await fetch(`${activityEndpoint}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' },
            });
            if (!response.ok) throw new Error('Aktivitas gagal dimuat.');

            const data = await response.json();
            if (!append) allActivityList.replaceChildren();
            data.items.forEach(entry => {
                const key = activityKey(entry);
                const exists = key && Array.from(allActivityList.children)
                    .some(item => item.dataset.activityKey === key);
                if (!exists) allActivityList.append(createActivityItem(entry));
            });

            activityPage = data.current_page;
            activityLastPage = data.last_page;
            activityCount = data.total;
            updateActivityCount();
            loadMoreActivitiesButton.classList.toggle('hidden', !data.has_more);
            if (!allActivityList.children.length) {
                const empty = document.createElement('li');
                empty.className = 'text-xs text-bank-gray';
                empty.textContent = 'Belum ada aktivitas hari ini.';
                allActivityList.append(empty);
            }
        }

        toggleActivitiesButton.addEventListener('click', async () => {
            activitiesExpanded = !activitiesExpanded;
            allActivityPanel.classList.toggle('hidden', !activitiesExpanded);
            updateActivityCount();
            if (activitiesExpanded && activityPage === 0) {
                try {
                    await loadActivities();
                    activityStatus.classList.add('hidden');
                } catch {
                    activityStatus.textContent = 'Aktivitas tidak dapat dimuat. Periksa koneksi lalu coba lagi.';
                    activityStatus.classList.remove('hidden');
                }
            }
        });

        loadMoreActivitiesButton.addEventListener('click', async () => {
            if (activityPage >= activityLastPage) return;
            loadMoreActivitiesButton.disabled = true;
            try {
                await loadActivities(activityPage + 1, true);
                activityStatus.classList.add('hidden');
            } catch {
                activityStatus.textContent = 'Aktivitas berikutnya gagal dimuat. Coba lagi.';
                activityStatus.classList.remove('hidden');
            } finally {
                loadMoreActivitiesButton.disabled = false;
            }
        });

        async function pollActivities() {
            if (activityPollRunning || document.hidden) return;
            activityPollRunning = true;
            try {
                const params = new URLSearchParams({ day: activityDay, since: activitySince });
                const response = await fetch(`${activityEndpoint}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok) throw new Error('Pembaruan aktivitas gagal.');
                const data = await response.json();
                activitySince = data.checked_at;
                data.items.forEach(entry => addRecent(entry, true));
                activityStatus.classList.add('hidden');
            } catch {
                activityStatus.textContent = 'Pembaruan aktivitas gagal. Periksa koneksi.';
                activityStatus.classList.remove('hidden');
            } finally {
                activityPollRunning = false;
            }
        }

        function showResult(data, suppressToast = false) {
            const variants = { success: 'success', warning: 'warning', error: 'error' };
            const variant = variants[data.status] || 'error';
            if (!suppressToast && typeof window.showToast === 'function') {
                window.showToast({
                    title: data.title || 'Hasil scan',
                    description: data.message || 'Status scan tidak tersedia.',
                    variant,
                    duration: variant === 'success' ? 3800 : 5200,
                    action: data.wa_url ? {
                        label: 'Kirim WhatsApp ke Tamu',
                        onClick: () => openWhatsApp(data.wa_url),
                    } : null,
                });
            } else if (!suppressToast) {
                console.error('Toast helper tidak tersedia.');
            }

            if (navigator.vibrate) navigator.vibrate(data.status === 'success' ? 80 : [60, 50, 60]);

            // Beep sukses / gagal supaya petugas tidak perlu menatap layar terus.
            playBeep(data.status === 'success' ? 'success' : 'error');

            // Otomatis buka chat WhatsApp tamu — nomor diambil dari form gate,
            // jadi petugas tidak perlu memilih/mengetik nomor lagi.
            if (data.wa_url) {
                openWhatsApp(data.wa_url);
            }
        }

        let waPopupBlocked = false;
        function openWhatsApp(url) {
            if (!url) return;
            // Ingat percobaan terakhir; kalau popup diblokir, tombol manual tetap tersedia.
            const win = window.open(url, 'wa_visitor_chat');
            if (!win) waPopupBlocked = true;
        }

        async function submitCode(code) {
            if (scanInProgress) return;
            if (!navigator.onLine) {
                updateScannerConnectionStatus();
                showResult({ status: 'error', title: 'Koneksi terputus', message: 'Scan belum diproses. Periksa koneksi, lalu pindai kembali setelah online.' });
                return;
            }
            scanInProgress = true;
            try {
                const res = await fetch('{{ route('gate.scan') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ code }),
                });
                const data = await res.json();
                if (res.status >= 500) {
                    updateScannerConnectionStatus(true);
                    showResult({ status: 'error', title: 'Server/database tidak tersedia', message: 'Status scan belum dapat dipastikan. Jangan scan berulang; minta petugas memeriksa riwayat tamu.' });
                    return;
                }
                updateScannerConnectionStatus();
                const eventKey = data.event ? activityKey(data.event) : '';
                showResult(data, Boolean(eventKey && seenActivityKeys.has(eventKey)));
                if (data.event) {
                    addRecent({
                        id: data.event.id,
                        visit_id: data.event.visit_id || data.event.id,
                        name: data.event.name,
                        direction: data.event.direction,
                        time: data.event.time || data.event.check_out || data.event.check_in,
                        label: data.event.direction === 'out' ? 'Checkout' : 'Check-in',
                        message: data.event.direction === 'out'
                            ? `Kunjungan selesai · durasi ${data.event.duration ?? '-'}`
                            : `Kedatangan tercatat pukul ${data.event.check_in ?? '-'}`,
                    });
                }
            } catch {
                updateScannerConnectionStatus(navigator.onLine);
                showResult({ status: 'error', title: 'Koneksi bermasalah', message: 'Status scan belum dapat dipastikan. Jangan scan berulang; minta petugas memeriksa riwayat tamu.' });
            } finally {
                scanInProgress = false;
            }
        }

        function updateScannerConnectionStatus(serverUnavailable = false) {
            const status = document.getElementById('scanner-connection-status');
            if (!navigator.onLine || serverUnavailable) {
                status.textContent = !navigator.onLine
                    ? 'Koneksi internet terputus. Scan tidak terkirim.'
                    : 'Server/database tidak tersedia. Periksa status tamu sebelum mencoba scan kembali.';
                status.classList.remove('hidden');
            } else {
                status.classList.add('hidden');
            }
        }
        window.addEventListener('online', () => updateScannerConnectionStatus());
        window.addEventListener('offline', () => updateScannerConnectionStatus());
        updateScannerConnectionStatus();

        function handleScan(decodedText) {
            if (Date.now() < lockUntil) return;
            lockUntil = Date.now() + 3000;
            submitCode(decodedText);
        }

        // --- Bunyi beep via Web Audio (tanpa file eksternal) ---
        let audioCtx = null;
        function playBeep(type = 'success') {
            try {
                audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
                if (audioCtx.state === 'suspended') audioCtx.resume();
                const now = audioCtx.currentTime;
                const seq = type === 'success'
                    ? [{ f: 880, t: 0, d: 0.12 }, { f: 1320, t: 0.12, d: 0.18 }]
                    : [{ f: 320, t: 0, d: 0.2 }, { f: 220, t: 0.2, d: 0.25 }];
                seq.forEach(({ f, t, d }) => {
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.value = f;
                    gain.gain.setValueAtTime(0.0001, now + t);
                    gain.gain.exponentialRampToValueAtTime(0.4, now + t + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + t + d);
                    osc.connect(gain).connect(audioCtx.destination);
                    osc.start(now + t);
                    osc.stop(now + t + d + 0.05);
                });
            } catch (e) { /* audio tidak tersedia — abaikan */ }
        }

        async function startCamera(silent = false) {
            if (scanning) return true;
            // AudioContext butuh gesture pengguna; siapkan di klik pertama mana pun.
            try {
                audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
                if (audioCtx.state === 'suspended') audioCtx.resume();
            } catch (e) {}
            html5QrCode = new Html5Qrcode('reader', { verbose: false });
            try {
                await html5QrCode.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 240, height: 240 } },
                    (decodedText) => handleScan(decodedText),
                    () => {}
                );
                scanning = true;
                document.getElementById('btn-start').classList.add('hidden');
                document.getElementById('btn-stop').classList.remove('hidden');
                return true;
            } catch (err) {
                if (!silent) showResult({ status: 'error', title: 'Kamera gagal dibuka', message: 'Berikan izin kamera atau gunakan input manual di bawah.' });
                return false;
            }
        }

        async function stopCamera() {
            if (!html5QrCode || !scanning) return;
            try { await html5QrCode.stop(); } catch (e) {}
            scanning = false;
            document.getElementById('btn-start').classList.remove('hidden');
            document.getElementById('btn-stop').classList.add('hidden');
        }

        document.getElementById('btn-start').addEventListener('click', () => startCamera(false));

        document.getElementById('btn-stop').addEventListener('click', stopCamera);
        updateActivityCount();
        pollActivities();
        window.setInterval(pollActivities, 10000);

        // Auto-start kamera saat halaman dibuka (perlu HTTPS — sudah terpenuhi di Vercel).
        // Kalau browser menolak autoplay tanpa gesture, tombol "Mulai Kamera" tetap tersedia.
        window.addEventListener('load', () => { startCamera(true); });
        // Fallback: coba lagi saat tab kembali terlihat (misal setelah balik dari tab WA).
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && !scanning) startCamera(true);
        });

        document.getElementById('manual-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const code = document.getElementById('manual-code').value.trim();
            if (code) submitCode(code);
            document.getElementById('manual-code').value = '';
        });

        // Tombol testing: isi token terakhir ke input manual + scroll ke atas.
        const btnFillTest = document.getElementById('btn-fill-test');
        if (btnFillTest) {
            btnFillTest.addEventListener('click', () => {
                const input = document.getElementById('manual-code');
                input.value = btnFillTest.dataset.token || '';
                input.focus();
                const behavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';
                input.scrollIntoView({ behavior, block: 'center' });
            });
        }
    </script>

@endpush
