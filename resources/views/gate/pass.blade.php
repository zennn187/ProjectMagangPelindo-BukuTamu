<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gate Pass — Checkout</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        :root { --navy:#091a3a; --blue:#1d4ed8; --ink:#20304f; --muted:#64748b; --line:#dbe5f2; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; font-family:Figtree,ui-sans-serif,system-ui,sans-serif; color:var(--ink); background:linear-gradient(145deg,#eef4fb,#f8fafc 55%,#e8f0fb); display:flex; justify-content:center; padding:28px 16px 44px; }
        .shell { width:min(100%,390px); }
        .pass { width:100%; overflow:hidden; border:1px solid rgba(148,163,184,.3); border-radius:26px; background:rgba(255,255,255,.9); box-shadow:0 24px 60px rgba(15,23,42,.12); }
        .pass-head { position:relative; overflow:hidden; padding:24px; color:#fff; background:linear-gradient(135deg,#091a3a,#123b78 68%,#1d4ed8); }
        .eyebrow { font-size:10px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:rgba(255,255,255,.66); }
        .pass-title { margin:14px 0 0; font-size:23px; line-height:1.1; letter-spacing:-.02em; }
        .pass-sub { margin:7px 0 0; color:rgba(255,255,255,.72); font-size:12px; }
        .status { position:absolute; right:20px; bottom:22px; padding:6px 10px; border-radius:999px; font-size:10px; font-weight:700; }
        .status.valid { border:1px solid rgba(134,239,172,.35); color:#dcfce7; background:rgba(22,163,74,.2); }
        .status.used { border:1px solid rgba(252,165,165,.4); color:#fee2e2; background:rgba(220,38,38,.25); }
        .qr-wrap { display:flex; flex-direction:column; align-items:center; padding:24px; }
        .qr-frame { display:grid; place-items:center; width:220px; height:220px; border:1px solid var(--line); border-radius:22px; background:#fff; box-shadow:0 12px 26px rgba(15,23,42,.08); }
        .qr-note { margin:14px 0 0; font-size:12px; color:var(--muted); text-align:center; }
        .meta { padding:4px 24px 20px; }
        .meta .row { display:flex; justify-content:space-between; gap:18px; padding:11px 0; border-bottom:1px solid var(--line); font-size:12px; }
        .meta .row span { color:var(--muted); }
        .meta .row strong { max-width:62%; color:var(--navy); text-align:right; overflow-wrap:anywhere; }
        .pass-foot { padding:14px 24px; color:var(--muted); background:#f7faff; font-size:10px; text-align:center; }
        .dim { filter:grayscale(1); opacity:.45; }
    </style>
</head>
<body>
    <div class="shell">
        <div class="pass">
            <div class="pass-head">
                <div class="eyebrow">Pelindo Regional 1 · Dumai</div>
                <h1 class="pass-title">Barcode Checkout</h1>
                <p class="pass-sub">Tunjukkan QR ini kepada petugas security saat keluar area.</p>
                @if($visit->checkout_used_at)
                    <span class="status used">Sudah Dipakai · {{ $visit->checkout_used_at->format('H:i') }}</span>
                @else
                    <span class="status valid">Aktif · Sekali Pakai</span>
                @endif
            </div>
            <div class="qr-wrap">
                <div class="qr-frame {{ $visit->checkout_used_at ? 'dim' : '' }}">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->color(9,26,58)->generate($visit->checkout_token) !!}
                </div>
                <p class="qr-note">
                    @if($visit->checkout_used_at)
                        Barcode ini sudah digunakan pada {{ $visit->checkout_used_at->format('d/m/Y H:i') }} dan tidak dapat dipakai lagi.
                    @else
                        Barcode hanya berlaku <strong>1x pemindaian</strong>. Screenshot halaman ini bila diperlukan.
                    @endif
                </p>
            </div>
            <div class="meta">
                <div class="row"><span>Nama</span><strong>{{ $visit->visitor_name }}</strong></div>
                <div class="row"><span>Instansi</span><strong>{{ $visit->visitor_institution }}</strong></div>
                <div class="row"><span>Menemui</span><strong>{{ $visit->person_to_meet ?? '-' }}</strong></div>
                <div class="row"><span>Jam Masuk</span><strong>{{ $visit->check_in_time?->format('d/m/Y H:i') }}</strong></div>
                @if($visit->check_out_time)
                    <div class="row"><span>Jam Keluar</span><strong>{{ $visit->check_out_time->format('d/m/Y H:i') }}</strong></div>
                    <div class="row"><span>Durasi</span><strong>{{ $visit->durationText() }}</strong></div>
                @endif
            </div>
            <div class="pass-foot">Buku Tamu Digital · PT. Pelindo Regional 1 Dumai</div>
        </div>
    </div>
</body>
</html>
