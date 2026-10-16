<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Kedatangan Tamu – Pelindo Regional 1 Dumai</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #091a3a;
            --blue: #2563eb;
            --blue-dark: #1e40af;
            --ink: #1e293b;
            --muted: #64748b;
            --line: #e2e8f0;
            --line-soft: #eef2f7;
            --ok: #16a34a;
            --ok-bg: #f0fdf4;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
            color: var(--ink);
            background: #edf2fa;
            padding: 0 16px 56px;
            overflow-x: hidden;
        }

        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(70px);
            pointer-events: none;
            z-index: -1;
        }

        body::before { width: 420px; height: 420px; top: -120px; right: -100px; background: rgba(37,99,235,.22); }
        body::after  { width: 380px; height: 380px; bottom: -120px; left: -110px; background: rgba(29,78,216,.18); }

        .band {
            margin: 0 -16px 28px;
            padding: 36px 16px 130px;
            background: linear-gradient(180deg, #0a1d44, #123b78 78%, #1a4a94);
            position: relative;
            overflow: hidden;
        }

        .band::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 280px; height: 280px;
            border: 1px solid rgba(147,197,253,.12);
            border-radius: 40px;
            transform: rotate(30deg);
            pointer-events: none;
        }

        .band::after {
            content: '';
            position: absolute;
            left: 50%; bottom: -46px;
            transform: translateX(-50%);
            width: 560px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,.07);
            filter: blur(8px);
        }

        .band-inner {
            max-width: 640px;
            margin: 0 auto;
            text-align: center;
            color: #fff;
            position: relative;
            z-index: 1;
        }

        .band .logo {
            width: 64px; height: 64px;
            border-radius: 18px;
            background: #fff;
            object-fit: contain;
            padding: 9px;
            margin: 0 auto 16px;
            display: block;
            box-shadow: 0 12px 28px rgba(2,6,23,.35);
            animation: logo-in .7s cubic-bezier(.22,1,.36,1) both;
        }

        .band h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: -.025em;
            font-weight: 800;
            animation: fade-up .6s ease .1s both;
        }

        .band p {
            margin: 8px 0 0;
            font-size: 13px;
            color: rgba(255,255,255,.75);
            animation: fade-up .6s ease .2s both;
        }

        .stepper {
            max-width: 640px;
            margin: -100px auto 24px;
            position: relative;
            animation: fade-up .6s ease .25s both;
        }

        .stepper-card {
            background: rgba(255,255,255,.82);
            -webkit-backdrop-filter: blur(18px) saturate(1.4);
            backdrop-filter: blur(18px) saturate(1.4);
            border: 1px solid rgba(255,255,255,.72);
            border-radius: 20px;
            box-shadow: 0 16px 45px rgba(9,26,58,.13);
            padding: 18px 24px;
        }

        .steps { display: flex; align-items: center; justify-content: center; }
        .step  { display: flex; align-items: center; gap: 10px; }

        .step-dot {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: 2px solid var(--line);
            background: #fff;
            color: var(--muted);
            display: grid;
            place-items: center;
            font-size: 13px; font-weight: 700;
            transition: all .3s cubic-bezier(.22,1,.36,1);
        }

        .step.done .step-dot   { border-color: var(--ok);   background: var(--ok);   color: #fff; }
        .step.active .step-dot { border-color: var(--blue);  color: var(--blue); box-shadow: 0 0 0 5px rgba(37,99,235,.12); }
        .step-label { font-size: 12.5px; font-weight: 600; color: var(--muted); }
        .step.done .step-label, .step.active .step-label { color: var(--navy); }

        .step-line {
            width: 50px; height: 2px;
            background: var(--line);
            border-radius: 2px;
            margin: 0 12px;
            position: relative; overflow: hidden;
        }

        .step-line.done::after {
            content: '';
            position: absolute; inset: 0;
            background: var(--ok);
            animation: line-fill .35s ease forwards;
        }

        @keyframes line-fill { from { transform: scaleX(0); transform-origin: left; } to { transform: scaleX(1); } }

        .shell {
            max-width: 640px;
            margin: 0 auto;
            animation: fade-up .65s ease .3s both;
        }

        .card {
            background: rgba(255,255,255,.78);
            -webkit-backdrop-filter: blur(20px) saturate(1.5);
            backdrop-filter: blur(20px) saturate(1.5);
            border: 1px solid rgba(255,255,255,.68);
            border-radius: 24px;
            box-shadow: 0 28px 65px rgba(15,23,42,.14), inset 0 1px 0 rgba(255,255,255,.72);
            overflow: hidden;
        }

        .section-head {
            padding: 20px 26px;
            border-bottom: 1px solid var(--line-soft);
            display: flex; align-items: center; gap: 14px;
            background: rgba(255,255,255,.5);
        }

        .section-icon {
            width: 38px; height: 38px;
            border-radius: 12px;
            background: rgba(37,99,235,.08);
            color: var(--blue);
            display: grid; place-items: center; flex-shrink: 0;
        }

        .section-head h2 { margin: 0; font-size: 15.5px; font-weight: 800; color: var(--navy); }
        .section-head p  { margin: 3px 0 0; font-size: 12px; color: var(--muted); }

        .card-body { padding: 24px 26px 28px; }

        .grid-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 18px;
        }

        .grid-form .full { grid-column: 1 / -1; }
        .field { display: flex; flex-direction: column; }

        label {
            font-size: 12.5px; font-weight: 700;
            color: var(--navy); margin-bottom: 7px; letter-spacing: -.01em;
        }

        label .req { color: #dc2626; }

        input, textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid rgba(148,163,184,.3);
            border-radius: 12px;
            font: inherit; font-size: 14px;
            color: var(--ink);
            background: rgba(255,255,255,.85);
            outline: none;
            transition: border-color .18s, box-shadow .18s, background .18s;
            box-shadow: inset 0 1px 2px rgba(9,26,58,.04);
        }

        input::placeholder, textarea::placeholder { color: #b0bfcc; }

        input:focus, textarea:focus {
            border-color: var(--blue);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(37,99,235,.10);
        }

        input:hover:not(:focus), textarea:hover:not(:focus) { border-color: rgba(148,163,184,.55); }

        input:valid:not(:placeholder-shown):not(:focus) {
            border-color: rgba(22,163,74,.4);
        }

        textarea { resize: vertical; min-height: 88px; }

        .cam-box {
            display: flex; flex-direction: column; align-items: center; gap: 14px;
            border: 2px dashed rgba(148,163,184,.4);
            border-radius: 18px; padding: 18px;
            background: rgba(248,250,252,.65);
            transition: border-color .25s, background .25s;
        }

        .cam-box video, .cam-box canvas {
            width: 100%; max-width: 240px; height: auto;
            aspect-ratio: 4/3; object-fit: cover;
            border-radius: 14px; background: #0f172a;
            box-shadow: 0 8px 24px rgba(9,26,58,.18);
        }

        .cam-placeholder {
            width: 100%; max-width: 240px; aspect-ratio: 4/3;
            border-radius: 14px; background: #0f172a;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 8px; color: rgba(255,255,255,.38); font-size: 12px;
        }

        .cam-placeholder svg { width: 2.2rem; height: 2.2rem; }
        .cam-placeholder span { font-weight: 600; letter-spacing: .06em; text-transform: uppercase; font-size: 10px; }

        .cam-actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; }

        .btn-mini {
            padding: 10px 18px; border-radius: 12px;
            background: #fff; color: var(--ink);
            font: inherit; font-size: 13px; font-weight: 600;
            cursor: pointer; border: 1.5px solid var(--line);
            transition: background .15s, border-color .15s, transform .18s, box-shadow .18s;
            display: inline-flex; align-items: center; gap: 6px;
        }

        .btn-mini:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(9,26,58,.09); }
        .btn-mini:active { transform: translateY(0); }

        .btn-mini.primary {
            background: var(--blue); color: #fff;
            border-color: var(--blue);
            box-shadow: 0 10px 24px rgba(37,99,235,.25);
        }

        .btn-mini.primary:hover { background: var(--blue-dark); box-shadow: 0 14px 28px rgba(37,99,235,.32); }

        .card-foot {
            padding: 20px 26px;
            border-top: 1px solid rgba(148,163,184,.15);
            background: rgba(255,255,255,.55);
        }

        .btn-submit {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 15px;
            border: 0; border-radius: 14px;
            background: linear-gradient(135deg, var(--blue), var(--blue-dark));
            color: #fff; font: inherit; font-size: 15.5px; font-weight: 800;
            cursor: pointer; letter-spacing: -.01em;
            transition: transform .2s cubic-bezier(.22,1,.36,1), box-shadow .2s, opacity .2s;
            box-shadow: 0 14px 35px rgba(37,99,235,.3);
            position: relative; overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.14), transparent);
            opacity: 0; transition: opacity .2s;
        }

        .btn-submit:hover            { transform: translateY(-2px); box-shadow: 0 22px 48px rgba(37,99,235,.38); }
        .btn-submit:hover::before    { opacity: 1; }
        .btn-submit:active           { transform: translateY(0); box-shadow: 0 10px 25px rgba(37,99,235,.25); }
        .btn-submit:disabled         { opacity: .6; cursor: not-allowed; transform: none; }

        .btn-submit .arrow-icon      { transition: transform .2s; }
        .btn-submit:hover .arrow-icon { transform: translateX(4px); }

        .spinner {
            display: none; width: 18px; height: 18px;
            border: 2.5px solid rgba(255,255,255,.4);
            border-top-color: #fff; border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        .btn-submit.loading .spinner    { display: inline-block; }
        .btn-submit.loading .arrow-icon { display: none; }

        @keyframes spin { to { transform: rotate(360deg); } }

        .foot-note { margin-top: 14px; text-align: center; font-size: 11.5px; color: var(--muted); }

        .result { display: none; text-align: center; padding: 48px 28px; }
        .result.show { display: block; animation: fade-up .5s cubic-bezier(.22,1,.36,1) both; }

        .result .ok-ring {
            width: 72px; height: 72px; margin: 0 auto 20px;
            border-radius: 50%; background: var(--ok-bg); color: var(--ok);
            display: grid; place-items: center; font-size: 32px;
            animation: pop-in .5s cubic-bezier(.34,1.6,.5,1) .1s both;
            box-shadow: 0 0 0 8px rgba(22,163,74,.08);
        }

        .result h2    { margin: 0 0 10px; color: var(--navy); font-size: 22px; font-weight: 800; }
        .result p     { margin: 0 auto; max-width: 440px; color: var(--muted); font-size: 13.5px; line-height: 1.7; }

        .result .again {
            margin-top: 22px; padding: 11px 22px;
            border-radius: 12px; background: var(--line-soft);
            color: var(--navy); font-size: 13px; font-weight: 700;
            cursor: pointer; border: 0; display: inline-block;
            transition: background .15s, transform .15s;
        }

        .result .again:hover { background: #dde6f5; transform: translateY(-1px); }

        .err {
            display: none; margin: 0 0 18px;
            padding: 12px 16px; border-radius: 13px;
            background: #fef2f2; border: 1.5px solid #fecaca;
            color: #b91c1c; font-size: 13px;
            animation: shake .4s ease;
        }

        .err.show { display: flex; align-items: flex-start; gap: 10px; }

        .foot-bottom { margin-top: 20px; text-align: center; font-size: 11px; color: var(--muted); }

        @keyframes logo-in  { from { opacity: 0; transform: translateY(-16px) scale(.85); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes fade-up  { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pop-in   { 0% { transform: scale(0); } 60% { transform: scale(1.1); } 100% { transform: scale(1); } }
        @keyframes shake    { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-6px); } 75% { transform: translateX(6px); } }

        @media (max-width: 560px) {
            .grid-form   { grid-template-columns: 1fr; }
            .band        { padding-top: 28px; }
            .step-label  { display: none; }
            .step-line   { width: 28px; }
            .card-body   { padding: 20px 18px 24px; }
            .card-foot   { padding: 18px; }
            .section-head { padding: 16px 18px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <div class="band">
        <div class="band-inner">
            <img src="{{ asset('images/pelindo-logo.png') }}" alt="Pelindo" class="logo">
            <h1>Form Kedatangan Tamu</h1>
            <p>Security Post · PT. Pelindo (Persero) Regional 1 Dumai</p>
        </div>
    </div>

    <div class="stepper">
        <div class="stepper-card">
            <div class="steps">
                <div class="step active" id="step-1">
                    <span class="step-dot" id="dot-1">1</span>
                    <span class="step-label">Data Kedatangan</span>
                </div>
                <div class="step-line" id="line-1"></div>
                <div class="step" id="step-2">
                    <span class="step-dot" id="dot-2">2</span>
                    <span class="step-label">Checkout Barcode</span>
                </div>
            </div>
        </div>
    </div>

    <div class="shell">
        <div class="card">
            <div class="card-body" id="form-wrap">
                <div class="err" id="form-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01" stroke-linecap="round"/></svg>
                    <span id="form-error-text"></span>
                </div>

                <div class="section-head" style="margin: -24px -26px 22px; border-radius: 24px 24px 0 0;">
                    <span class="section-icon">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"/></svg>
                    </span>
                    <div>
                        <h2>Data Kedatangan</h2>
                        <p>Lengkapi informasi Anda untuk dicatat petugas.</p>
                    </div>
                </div>

                <form id="gate-form" novalidate>
                    <div class="grid-form">
                        <div class="field">
                            <label>Nama Lengkap <span class="req">*</span></label>
                            <input type="text" name="visitor_name" required maxlength="255" placeholder="Nama sesuai identitas">
                        </div>

                        <div class="field">
                            <label>Ingin Menemui <span class="req">*</span></label>
                            <input type="text" name="person_to_meet" required maxlength="255" placeholder="Nama pegawai yang dituju">
                        </div>

                        <div class="field">
                            <label>Asal Instansi <span class="req">*</span></label>
                            <input type="text" name="visitor_institution" required maxlength="255" placeholder="Perusahaan / instansi">
                        </div>

                        <div class="field">
                            <label>No. WhatsApp <span class="req">*</span></label>
                            <input type="tel" name="visitor_phone" required maxlength="20" placeholder="081234567890">
                        </div>

                        <div class="field">
                            <label>NIK (16 Digit) <span class="req">*</span></label>
                            <input type="text" name="identity_number" required inputmode="numeric" maxlength="16" pattern="[0-9]{16}" placeholder="3174xxxxxxxxxxxx">
                        </div>

                        <div class="field">
                            <label>Foto Tamu <span style="font-weight:400;color:var(--muted);font-size:11px;">(opsional)</span></label>
                            <div class="cam-box" id="cam-box">
                                <video id="cam" autoplay playsinline muted style="display:none;"></video>
                                <canvas id="cam-shot" hidden></canvas>
                                <div class="cam-placeholder" id="cam-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                                    <span>Kamera belum aktif</span>
                                </div>
                                <div class="cam-actions">
                                    <button type="button" class="btn-mini primary" id="cam-start">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                                        Nyalakan Kamera
                                    </button>
                                    <button type="button" class="btn-mini" id="cam-capture" hidden>Ambil Foto</button>
                                    <button type="button" class="btn-mini" id="cam-retake" hidden>Ulangi</button>
                                </div>
                            </div>
                        </div>

                        <div class="field full">
                            <label>Keperluan <span class="req">*</span></label>
                            <textarea name="purpose" required maxlength="1000" placeholder="Jelaskan keperluan kunjungan Anda"></textarea>
                        </div>

                        <div class="field full">
                            <label style="display:flex;gap:8px;align-items:flex-start;font-weight:400;font-size:12.5px;color:var(--ink);cursor:pointer;">
                                <input type="checkbox" name="consent" value="1" required style="width:auto;margin-top:3px;">
                                <span>Saya setuju data (nama, NIK, kontak, foto) digunakan untuk pencatatan buku tamu Pelindo dan disimpan sesuai <a href="/privasi" target="_blank" style="color:var(--blue);">Kebijakan Privasi</a>. <span class="req">*</span></span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-foot">
                <button type="button" class="btn-submit" id="btn-submit" onclick="submitForm()">
                    <span>Daftar Kedatangan</span>
                    <svg class="arrow-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    <span class="spinner"></span>
                </button>
                <p class="foot-note">Data Anda aman &amp; hanya digunakan untuk keperluan pencatatan kunjungan.</p>
            </div>

            <div class="result" id="result">
                <div class="ok-ring">✓</div>
                <h2>Pendaftaran Berhasil</h2>
                <p id="result-text"></p>
                <button type="button" class="again" onclick="resetForm()">Daftarkan Tamu Lain</button>
            </div>
        </div>

        <p class="foot-bottom">Buku Tamu Digital · PT. Pelindo Regional 1 Dumai · <a href="/privasi" style="color:var(--blue);">Kebijakan Privasi</a> · Cookie yang digunakan hanya session esensial (laravel_session, XSRF-TOKEN).</p>
    </div>

    <script>
        const camVideo = document.getElementById('cam');
        const camShot  = document.getElementById('cam-shot');
        const camPH    = document.getElementById('cam-placeholder');
        let stream = null, photoData = null;

        function setStep(done) {
            const dot1  = document.getElementById('dot-1');
            const line1 = document.getElementById('line-1');
            const step2 = document.getElementById('step-2');
            if (done) {
                document.getElementById('step-1').classList.replace('active','done');
                line1.classList.add('done');
                dot1.textContent = '✓';
                step2.classList.add('active');
            } else {
                document.getElementById('step-1').classList.add('active');
                document.getElementById('step-1').classList.remove('done');
                line1.classList.remove('done');
                dot1.textContent = '1';
                step2.classList.remove('active');
            }
        }

        document.getElementById('cam-start').addEventListener('click', async () => {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                camVideo.srcObject = stream;
                camVideo.style.display = '';
                camPH.style.display = 'none';
                document.getElementById('cam-start').hidden   = true;
                document.getElementById('cam-capture').hidden = false;
            } catch {
                showError('Kamera tidak dapat diakses. Lanjutkan tanpa foto atau izinkan kamera di browser.');
            }
        });

        document.getElementById('cam-capture').addEventListener('click', () => {
            const c = camShot.getContext('2d');
            camShot.width  = camVideo.videoWidth  || 640;
            camShot.height = camVideo.videoHeight || 480;
            c.drawImage(camVideo, 0, 0, camShot.width, camShot.height);
            photoData = camShot.toDataURL('image/jpeg', 0.85);
            camVideo.style.display = 'none';
            camShot.hidden = false;
            stopStream();
            document.getElementById('cam-capture').hidden = true;
            document.getElementById('cam-retake').hidden  = false;
        });

        document.getElementById('cam-retake').addEventListener('click', async () => {
            photoData = null;
            camShot.hidden = true;
            camPH.style.display = 'none';
            document.getElementById('cam-retake').hidden  = true;
            document.getElementById('cam-capture').hidden = false;
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                camVideo.srcObject = stream;
                camVideo.style.display = '';
            } catch {}
        });

        function stopStream() {
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        }

        function showError(msg) {
            const el   = document.getElementById('form-error');
            const text = document.getElementById('form-error-text');
            text.textContent = msg;
            el.classList.add('show');
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function validateForm() {
            const form = document.getElementById('gate-form');
            for (const el of form.querySelectorAll('[required]')) {
                if (el.type === 'checkbox') {
                    if (!el.checked) {
                        showError('Centang persetujuan penggunaan data untuk melanjutkan.');
                        el.focus();
                        return false;
                    }
                    continue;
                }
                if (!el.value.trim()) {
                    showError('Lengkapi kolom wajib sebelum mendaftar.');
                    el.focus();
                    return false;
                }
            }
            const nik = form.querySelector('[name="identity_number"]');
            if (nik && !/^[0-9]{16}$/.test(nik.value.trim())) {
                showError('NIK harus 16 digit angka.');
                nik.focus();
                return false;
            }
            return true;
        }

        async function submitForm() {
            document.getElementById('form-error').classList.remove('show');
            if (!validateForm()) return;

            const form    = document.getElementById('gate-form');
            const btn     = document.getElementById('btn-submit');
            const payload = Object.fromEntries(new FormData(form).entries());
            if (photoData) payload.photo = photoData;

            btn.disabled = true;
            btn.classList.add('loading');
            setStep(true);

            try {
                const res  = await fetch('{{ route('gate.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok) {
                    setStep(false);
                    showError(data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Terjadi kesalahan. Periksa kembali data Anda.'));
                    return;
                }
                stopStream();
                document.getElementById('form-wrap').style.display  = 'none';
                document.querySelector('.card-foot').style.display   = 'none';
                document.getElementById('result-text').textContent   = data.message;
                document.getElementById('result').classList.add('show');
                setStep(true);
                document.getElementById('step-2').classList.remove('active');
                document.getElementById('dot-2').textContent = '✓';
            } catch {
                setStep(false);
                showError('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
            } finally {
                btn.disabled = false;
                btn.classList.remove('loading');
            }
        }

        function resetForm() {
            document.getElementById('gate-form').reset();
            photoData      = null;
            camShot.hidden = true;
            camVideo.style.display = 'none';
            camPH.style.display    = '';
            document.getElementById('cam-capture').hidden = true;
            document.getElementById('cam-retake').hidden  = true;
            document.getElementById('cam-start').hidden   = false;
            setStep(false);
            document.getElementById('form-wrap').style.display  = '';
            document.querySelector('.card-foot').style.display   = '';
            document.getElementById('result').classList.remove('show');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>
