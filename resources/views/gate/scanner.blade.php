@extends('layouts.operator')

@section('page_title', 'Scanner Gate')

@push('header_actions')
    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Live
    </span>
@endpush

@section('content')
    @if(!empty($dbDown))
        <div class="mb-4 rounded-2xl bg-amber-50 p-4 text-sm text-amber-800 ring-1 ring-amber-200">
            Database belum terhubung — hasil scan tidak bisa diproses sampai <strong>DB online</strong> diisi di Environment Variables lalu <strong>Redeploy</strong>.
        </div>
    @endif
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

            <div id="scan-result" class="hidden mt-5 rounded-2xl p-4 sm:p-5" role="status"></div>
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

            <div class="bg-amber-50 rounded-3xl p-5 sm:p-6 shadow-sm ring-1 ring-amber-200">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="font-semibold text-bank-navy">🧪 Testing Scan</h2>
                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-700">Dev only</span>
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
                    <h2 class="font-semibold text-bank-navy">Aktivitas Terakhir</h2>
                    <a href="{{ route('gate-reports.index') }}" class="text-xs font-medium text-bank-blue hover:underline">Laporan →</a>
                </div>
                <ul id="recent-scans" class="space-y-3 text-sm text-bank-gray">
                    <li class="text-xs text-bank-gray">Belum ada aktivitas pada sesi ini.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        let html5QrCode = null, scanning = false, lockUntil = 0;
        const recentList = document.getElementById('recent-scans');
        let hasRecent = false;

        function addRecent(entry) {
            if (!hasRecent) { recentList.innerHTML = ''; hasRecent = true; }
            const li = document.createElement('li');
            li.className = 'flex items-start justify-between gap-3 border-b border-bank-light pb-2';
            li.innerHTML = `<div><p class="font-semibold text-bank-navy">${entry.name}</p><p class="text-xs text-bank-gray">${entry.message}</p></div><span class="text-xs whitespace-nowrap text-bank-gray">${entry.time}</span>`;
            recentList.prepend(li);
            while (recentList.children.length > 6) recentList.lastChild.remove();
        }

        function showResult(data) {
            const box = document.getElementById('scan-result');
            const styles = {
                success: 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200',
                warning: 'bg-amber-50 text-amber-800 ring-1 ring-amber-200',
                error: 'bg-red-50 text-red-800 ring-1 ring-red-200',
            };
            const icons = { success: '✓', warning: '!', error: '✕' };
            box.className = 'mt-5 rounded-2xl p-4 sm:p-5 ' + (styles[data.status] || styles.error);
            box.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white text-lg font-bold ring-1 ring-black/5">${icons[data.status] || icons.error}</span>
                    <div class="flex-1">
                        <p class="font-bold text-sm">${data.title}</p>
                        <p class="text-sm mt-1 leading-relaxed">${data.message}</p>
                        ${data.wa_url ? `<button type="button" id="wa-send-btn" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition-colors">Kirim WhatsApp ke Tamu</button>` : ''}
                    </div>
                </div>`;
            box.classList.remove('hidden');
            if (navigator.vibrate) navigator.vibrate(data.status === 'success' ? 80 : [60, 50, 60]);

            // Beep sukses / gagal supaya petugas tidak perlu menatap layar terus.
            playBeep(data.status === 'success' ? 'success' : 'error');

            // Otomatis buka chat WhatsApp tamu — nomor diambil dari form gate,
            // jadi petugas tidak perlu memilih/mengetik nomor lagi.
            if (data.wa_url) {
                openWhatsApp(data.wa_url);
                const waBtn = document.getElementById('wa-send-btn');
                if (waBtn) waBtn.addEventListener('click', () => openWhatsApp(data.wa_url));
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
            const res = await fetch('{{ route('gate.scan') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ code }),
            });
            const data = await res.json();
            showResult(data);
            if (data.event) {
                addRecent({
                    name: data.event.name,
                    message: data.event.check_out ? `Checkout · durasi ${data.event.duration ?? '-'}` : `Check-in pukul ${data.event.check_in}`,
                    time: data.event.check_out || data.event.check_in,
                });
            }
        }

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
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }
    </script>

@endpush
