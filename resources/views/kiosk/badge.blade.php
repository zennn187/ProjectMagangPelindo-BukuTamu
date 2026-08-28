<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Badge Kunjungan</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <style>
            body { font-family: Figtree, ui-sans-serif, system-ui, sans-serif; background: #f1f5f9; display: flex; justify-content: center; padding: 40px 16px; }
            .badge { width: 340px; border-radius: 16px; overflow: hidden; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,.1); }
            .badge-head { background: #047857; color: #fff; padding: 18px 20px; }
            .qr { padding: 24px; display: flex; flex-direction: column; align-items: center; }
            .meta { padding: 0 20px 20px; text-align: center; }
            .meta .row { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; border-bottom: 1px dashed #e2e8f0; }
            .btn { margin-top: 20px; display: inline-block; background: #047857; color: #fff; padding: 10px 22px; border-radius: 10px; text-decoration: none; font-weight: 600; cursor: pointer; border: 0; }
            @media print { body { padding: 0; background: #fff; } .noprint { display: none !important; } }
        </style>
    </head>
    <body>
        <div>
            <div style="text-align:center; margin-bottom:16px;" class="noprint">
                <button onclick="window.print()" class="btn">Cetak Badge</button>
                <button onclick="window.close()" class="btn" style="background:#64748b;">Tutup</button>
            </div>
            <div class="badge">
                <div class="badge-head">
                    <div style="font-size:13px;">PT. Pelindo Regional 1 Dumai</div>
                    <div style="font-weight:700; font-size:18px; margin-top:2px;">BADGE TAMU</div>
                </div>
                <div class="qr">
                    {!! $visit->qrCodeSvg() !!}
                </div>
                <div class="meta">
                    <div class="row"><span>Nama</span><strong>{{ $visit->visitor_name }}</strong></div>
                    <div class="row"><span>Instansi</span><strong>{{ $visit->visitor_institution }}</strong></div>
                    <div class="row"><span>Tujuan</span><strong>{{ $visit->employee?->name ?? '-' }}</strong></div>
                    <div class="row"><span>Keperluan</span><strong>{{ Str::limit($visit->purpose, 40) }}</strong></div>
                    <div class="row"><span>Tanggal</span><strong>{{ $visit->created_at?->format('d/m/Y H:i') }}</strong></div>
                </div>
            </div>
        </div>
    </body>
</html>