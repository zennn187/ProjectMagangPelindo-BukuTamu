<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kebijakan Privasi — Buku Tamu Pelindo</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { margin:0; font-family:Figtree,ui-sans-serif,system-ui,sans-serif; background:#f5f7fb; color:#1e293b; }
        .wrap { max-width:760px; margin:0 auto; padding:40px 20px 64px; }
        .card { background:#fff; border:1px solid #e6edf8; border-radius:20px; padding:32px; box-shadow:0 18px 45px rgba(15,23,42,.05); }
        h1 { margin:0 0 8px; font-size:24px; color:#091a3a; }
        h2 { margin-top:24px; font-size:16px; color:#091a3a; }
        p, li { font-size:14px; line-height:1.7; color:#53627a; }
        a { color:#1d4ed8; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Kebijakan Privasi &amp; Cookie</h1>
        <p>Terakhir diperbarui: {{ date('d F Y') }} · Buku Tamu Digital PT. Pelindo Regional 1 Dumai.</p>
        <h2>1. Data yang kami kumpulkan</h2>
        <ul>
            <li>Jalur kiosk: nama, nomor WhatsApp, instansi, pegawai tujuan, keperluan, foto wajah.</li>
            <li>Jalur gate/security: nama, <strong>NIK (16 digit)</strong> sebagai tanda pengenal, instansi, orang yang dituju, keperluan, nomor WhatsApp, foto (opsional). <strong>Nomor plat kendaraan tidak lagi diminta.</strong></li>
        </ul>
        <h2>2. Tujuan penggunaan</h2>
        <p>Pencatatan kunjungan, verifikasi resepsionis/security, penerbitan QR badge &amp; barcode checkout sekali pakai, dan pelaporan internal. Tidak untuk iklan.</p>
        <h2>3. Dasar persetujuan</h2>
        <p>Dengan mencentang persetujuan pada formulir, Anda menyetujui pemrosesan data sesuai UU Pelindungan Data Pribadi (UU No. 27/2022). Anda dapat menarik persetujuan dengan menghubungi resepsionis.</p>
        <h2>4. Penyimpanan &amp; retensi</h2>
        <p>Data tersimpan di database MySQL internal. Foto kunjungan dihapus berkala (maksimal 1 tahun). Backup hanya untuk keperluan operasional.</p>
        <h2>5. Berbagi data</h2>
        <p>Data tidak dijual. Notifikasi WhatsApp dikirim via penyedia API WA resmi yang Anda daftarkan. Link checkout berisi token acak sekali pakai.</p>
        <h2>6. Hak Anda</h2>
        <p>Anda berhak meminta akses, koreksi, dan penghapusan data dengan menunjukkan NIK &amp; waktu kunjungan kepada admin/resepsionis.</p>
        <h2>7. Cookie</h2>
        <p>Kami hanya memakai cookie esensial: <code>laravel_session</code> dan <code>XSRF-TOKEN</code> untuk login &amp; keamanan CSRF. Tidak ada pelacakan iklan/analitik pihak ketiga.</p>
        <h2>8. Kontak</h2>
        <p>Resepsionis Pelindo Regional 1 Dumai, Pelabuhan Dumai, Kota Dumai, Riau. Situs: <a href="https://pelindo.id">pelindo.id</a>.</p>
        <p><a href="/">← Kembali ke kiosk</a> · <a href="/gate/form">Form gate</a></p>
    </div>
</div>
</body>
</html>
