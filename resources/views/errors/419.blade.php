<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sesi Kedaluwarsa &mdash; {{ config('app.name', 'Buku Tamu Pelindo') }}</title>
        <style>
            * { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                background: #f5f7fa;
                color: #343c6a;
                font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Figtree, Arial, sans-serif;
                -webkit-font-smoothing: antialiased;
            }

            .card {
                width: 100%;
                max-width: 460px;
                padding: 36px 32px;
                background: #ffffff;
                border: 1px solid #e6eff5;
                border-radius: 20px;
                box-shadow: 0 18px 40px rgba(52, 60, 106, 0.08);
                text-align: center;
            }

            .badge {
                width: 60px;
                height: 60px;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 18px;
                background: #eef2ff;
                color: #4f46e5;
                font-size: 26px;
                font-weight: 700;
            }

            h1 {
                margin: 0 0 10px;
                font-size: 22px;
                font-weight: 700;
                color: #1e1b4b;
            }

            p {
                margin: 0 0 8px;
                font-size: 14px;
                line-height: 1.65;
                color: #718ebf;
            }

            .hint {
                margin: 18px 0 26px;
                padding: 12px 14px;
                border-radius: 12px;
                background: #f8fafc;
                border: 1px solid #e6eff5;
                font-size: 13px;
                color: #343c6a;
                text-align: left;
            }

            .actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                display: inline-block;
                padding: 12px 18px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                transition: opacity .15s ease;
            }

            .btn-primary { background: #4f46e5; color: #ffffff; }
            .btn-secondary { background: #f5f7fa; color: #343c6a; border: 1px solid #e6eff5; }
            .btn:hover { opacity: .88; }

            footer {
                margin-top: 24px;
                font-size: 12px;
                color: #a0aec0;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="badge">419</div>

            <h1>Sesi Kedaluwarsa</h1>

            <p>
                Untuk alasan keamanan, halaman yang Anda kirim sudah tidak berlaku lagi
                (token keamanan/session tidak cocok).
            </p>

            <div class="hint">
                <strong>Penyebab umum:</strong>
                <br>&bull; halaman login dibuka terlalu lama (lebih dari 120 menit), atau
                <br>&bull; cookie session pada browser terhapus/berubah, atau
                <br>&bull; aplikasi diakses bergantian lewat alamat berbeda (mis. 127.0.0.1 dan URL tunnel).
            </div>

            <div class="actions">
                <a class="btn btn-primary" href="{{ route('login') }}">Kembali ke Halaman Login</a>
                <a class="btn btn-secondary" href="{{ url('/') }}">Buka Halaman Kiosk</a>
            </div>

            <footer>&copy; {{ date('Y') }} PT Pelindo &mdash; Sistem Buku Tamu</footer>
        </div>
    </body>
</html>
