@extends('layouts.operator')

@section('page_title', 'Scanner Gate')

@push('header_actions')
    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Live
    </span>
@endpush

@section('content')
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
                    <div>
                        <p class="font-bold text-sm">${data.title}</p>
                        <p class="text-sm mt-1 leading-relaxed">${data.message}</p>
                    </div>
                </div>`;
            box.classList.remove('hidden');
            if (navigator.vibrate) navigator.vibrate(data.status === 'success' ? 80 : [60, 50, 60]);
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

        document.getElementById('btn-start').addEventListener('click', async () => {
            if (scanning) return;
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
            } catch (err) {
                showResult({ status: 'error', title: 'Kamera gagal dibuka', message: 'Berikan izin kamera atau gunakan input manual di bawah.' });
            }
        });

        document.getElementById('btn-stop').addEventListener('click', async () => {
            if (!html5QrCode || !scanning) return;
            try { await html5QrCode.stop(); } catch (e) {}
            scanning = false;
            document.getElementById('btn-start').classList.remove('hidden');
            document.getElementById('btn-stop').classList.add('hidden');
        });

        document.getElementById('manual-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const code = document.getElementById('manual-code').value.trim();
            if (code) submitCode(code);
            document.getElementById('manual-code').value = '';
        });
    </script>

@endpush
