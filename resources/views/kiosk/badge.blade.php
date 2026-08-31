<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Badge Kunjungan</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite('resources/js/badge.js')
        <style>
            :root { --navy: #091a3a; --blue: #1d4ed8; --green: #16a34a; --ink: #20304f; --muted: #64748b; --line: #dbe5f2; }
            * { box-sizing: border-box; }
            body { min-height: 100vh; margin: 0; font-family: Figtree, ui-sans-serif, system-ui, sans-serif; color: var(--ink); background: radial-gradient(circle at top left, rgba(59,130,246,.12), transparent 30%), linear-gradient(145deg, #eef4fb, #f8fafc 55%, #e8f0fb); display: flex; justify-content: center; padding: 28px 16px 44px; }
            .page-shell { width: min(100%, 390px); }
            .actions { display: flex; gap: 10px; justify-content: center; margin-bottom: 16px; }
            .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; padding: 10px 16px; border: 1px solid transparent; border-radius: 12px; color: #fff; background: var(--blue); box-shadow: 0 10px 24px rgba(29,78,216,.2); font-size: 13px; font-weight: 700; cursor: pointer; transition: transform .2s ease, box-shadow .2s ease, background .2s ease; }
            .btn:hover { transform: translateY(-1px); box-shadow: 0 14px 28px rgba(29,78,216,.26); background: #1e40af; }
            .btn.secondary { color: var(--navy); background: rgba(255,255,255,.68); border-color: rgba(148,163,184,.35); box-shadow: 0 8px 20px rgba(15,23,42,.06); }
            .btn.secondary:hover { background: #fff; }
            .badge { width: 100%; overflow: hidden; border: 1px solid rgba(148,163,184,.3); border-radius: 28px; background: rgba(255,255,255,.82); box-shadow: 0 24px 60px rgba(15,23,42,.12); backdrop-filter: blur(14px); }
            .badge-head { position: relative; overflow: hidden; padding: 24px 24px 22px; color: #fff; background: linear-gradient(135deg, #091a3a, #123b78 68%, #1d4ed8); }
            .badge-head::after { content: ''; position: absolute; right: -36px; top: -48px; width: 150px; height: 150px; border: 1px solid rgba(147,197,253,.25); border-radius: 38px; transform: rotate(30deg); }
            .brand-line { position: relative; z-index: 1; display: flex; align-items: center; gap: 10px; }
            .brand-mark { display: inline-flex; width: 38px; height: 38px; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,.24); border-radius: 12px; background: rgba(255,255,255,.1); font-size: 18px; font-weight: 700; }
            .eyebrow { font-size: 10px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: rgba(255,255,255,.66); }
            .badge-title { margin: 18px 0 0; font-size: 24px; line-height: 1.1; letter-spacing: -.03em; }
            .badge-subtitle { margin: 7px 0 0; color: rgba(255,255,255,.72); font-size: 12px; }
            .status { position: absolute; right: 20px; bottom: 22px; z-index: 1; padding: 6px 10px; border: 1px solid rgba(134,239,172,.35); border-radius: 999px; color: #dcfce7; background: rgba(22,163,74,.2); font-size: 10px; font-weight: 700; }
            .qr { display: flex; flex-direction: column; align-items: center; padding: 24px 24px 20px; }
            .qr-frame { display: flex; width: 210px; height: 210px; align-items: center; justify-content: center; border: 1px solid var(--line); border-radius: 22px; background: #fff; box-shadow: 0 12px 26px rgba(15,23,42,.08); cursor: zoom-in; transition: transform .2s ease, box-shadow .2s ease; }
            .qr-frame:hover { transform: translateY(-2px); box-shadow: 0 16px 32px rgba(15,23,42,.13); }
            .qr-frame:focus-visible { outline: 3px solid rgba(29,78,216,.3); outline-offset: 4px; }
            .qr-frame svg { max-width: 176px; max-height: 176px; }
            .qr-note { margin: 14px 0 0; color: var(--muted); font-size: 11px; text-align: center; }
            .qr-modal { position: fixed; inset: 0; z-index: 20; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(9,26,58,.72); backdrop-filter: blur(12px); }
            .qr-modal[hidden] { display: none; }
            .qr-modal-card { position: relative; width: min(100%, 440px); padding: 18px; border: 1px solid rgba(255,255,255,.3); border-radius: 28px; background: rgba(255,255,255,.92); box-shadow: 0 30px 90px rgba(2,6,23,.42); text-align: center; }
            .qr-modal-title { margin: 2px 0 4px; color: var(--navy); font-size: 18px; font-weight: 700; }
            .qr-modal-copy { margin: 0 0 16px; color: var(--muted); font-size: 12px; }
            .qr-modal-frame { display: flex; min-height: min(70vh, 360px); align-items: center; justify-content: center; padding: 24px; border: 1px solid var(--line); border-radius: 22px; background: #fff; }
            .qr-modal-frame svg { width: min(100%, 300px); height: auto; max-height: min(60vh, 300px); }
            .qr-modal-close { position: absolute; top: -14px; right: -14px; display: inline-flex; width: 42px; height: 42px; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,.8); border-radius: 999px; color: var(--navy); background: #fff; box-shadow: 0 10px 24px rgba(2,6,23,.24); font-size: 22px; line-height: 1; cursor: pointer; }
            .meta { padding: 2px 24px 24px; }
            .meta .row { display: flex; justify-content: space-between; gap: 18px; padding: 11px 0; border-bottom: 1px solid var(--line); font-size: 12px; }
            .meta .row span { color: var(--muted); }
            .meta .row strong { max-width: 62%; color: var(--navy); font-weight: 700; text-align: right; overflow-wrap: anywhere; }
            .badge-footer { padding: 14px 24px; color: var(--muted); background: #f7faff; font-size: 10px; text-align: center; }
            @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }
            @media print { body { min-height: auto; padding: 0; background: #fff; } .noprint { display: none !important; } .badge { border: 0; box-shadow: none; } }
        </style>
    </head>
    <body>
        <div class="page-shell">
            <div class="actions noprint">
                <button onclick="window.print()" class="btn">Cetak Badge</button>
                <button onclick="window.close()" class="btn secondary">Tutup</button>
            </div>
            <div class="badge">
                <div class="badge-head">
                    <div class="brand-line">
                        <span class="brand-mark">P</span>
                        <div>
                            <div class="eyebrow">Pelindo Regional 1</div>
                            <div style="margin-top:2px; font-size:13px; font-weight:700;">Dumai</div>
                        </div>
                    </div>
                    <h1 class="badge-title">Badge Tamu</h1>
                    <p class="badge-subtitle">Tunjukkan QR ini kepada petugas saat tiba.</p>
                    <span class="status">Terverifikasi</span>
                </div>
                <div class="qr">
                    <button type="button" class="qr-frame" id="qr-zoom-trigger" aria-label="Perbesar QR code untuk dipindai">
                        {!! $visit->qrCodeSvg() !!}
                    </button>
                    <p class="qr-note">Scan untuk memvalidasi kunjungan</p>
                </div>
                <div class="meta">
                    <div class="row"><span>Nama</span><strong>{{ $visit->visitor_name }}</strong></div>
                    <div class="row"><span>Instansi</span><strong>{{ $visit->visitor_institution }}</strong></div>
                    <div class="row"><span>Tujuan</span><strong>{{ $visit->employee?->name ?? '-' }}</strong></div>
                    <div class="row"><span>Keperluan</span><strong>{{ Str::limit($visit->purpose, 40) }}</strong></div>
                    <div class="row"><span>Tanggal</span><strong>{{ $visit->created_at?->format('d/m/Y H:i') }}</strong></div>
                </div>
                <div class="badge-footer">Buku Tamu Digital · PT. Pelindo Regional 1 Dumai</div>
            </div>
        </div>
        <div class="qr-modal" id="qr-modal" hidden role="dialog" aria-modal="true" aria-labelledby="qr-modal-title">
            <div class="qr-modal-card">
                <button type="button" class="qr-modal-close" id="qr-modal-close" aria-label="Tutup QR code">&times;</button>
                <h2 class="qr-modal-title" id="qr-modal-title">QR Badge Tamu</h2>
                <p class="qr-modal-copy">Arahkan kamera petugas ke QR code ini.</p>
                <div class="qr-modal-frame" id="qr-modal-frame"></div>
            </div>
        </div>
    </body>
</html>
