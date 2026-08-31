<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Kiosk — Buku Tamu Pelindo</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --bank-navy: #091a3a;
                --bank-blue: #1d4ed8;
                --bank-light: #e6edf8;
                --bank-bg: #f5f7fb;
                --bank-gray: #53627a;
                --bank-green: #22c55e;
            }

            /* Non-mirror (default): tampil apa adanya (sesuai sensor kamera). */
            .mirror-y { transform: scaleX(-1); }
            /* Smooth scroll untuk navigasi anchor landing (#formulir, #layanan, dst). */
            html { scroll-behavior: smooth; }
            /* Sembunyikan konten Alpine sebelum inisialisasi (anti-flash). */
            [x-cloak] { display: none !important; }

            body {
                background:
                    radial-gradient(circle at top left, rgba(59,130,246,0.08), transparent 24%),
                    radial-gradient(circle at bottom right, rgba(37,99,235,0.10), transparent 18%),
                    var(--bank-bg);
            }

            .hero-orb {
                position: absolute;
                border-radius: 9999px;
                filter: blur(42px);
                opacity: 0.55;
                animation: float 12s ease-in-out infinite alternate;
                pointer-events: none;
                will-change: transform;
            }

            .hero-orb.one {
                width: 18rem;
                height: 18rem;
                top: 14%;
                left: 8%;
                background: rgba(96,165,250,0.30);
            }

            .hero-orb.two {
                width: 16rem;
                height: 16rem;
                right: 12%;
                bottom: 8%;
                background: rgba(59,130,246,0.26);
                animation-delay: 1.2s;
            }

            .hero-hexagon {
                position: absolute;
                top: 16%;
                right: 8%;
                z-index: 1;
                width: clamp(7rem, 16vw, 12rem);
                color: rgba(147, 197, 253, 0.2);
                opacity: 0.7;
                filter: drop-shadow(0 12px 20px rgba(2, 12, 35, 0.12));
                animation: hero-hexagon-float 11s ease-in-out infinite;
                pointer-events: none;
                will-change: transform;
            }

            .hero-hexagon svg {
                display: block;
                width: 100%;
                height: auto;
            }

            .success-modal-card {
                animation: success-modal-in 420ms cubic-bezier(0.22, 1, 0.36, 1) both;
            }

            .success-check {
                animation: success-check-pop 500ms ease-out 120ms both;
            }

            .success-check path {
                stroke-dasharray: 24;
                stroke-dashoffset: 24;
                animation: success-check-draw 500ms ease-out 220ms forwards;
            }

            .badge-link-entrance {
                animation: badge-link-in 450ms ease-out 260ms both;
            }

            .submit-spinner {
                display: inline-block;
                width: 1rem;
                height: 1rem;
                border: 2px solid currentColor;
                border-right-color: transparent;
                border-radius: 9999px;
                animation: submit-spin 700ms linear infinite;
                vertical-align: -0.15rem;
            }

            .glass-soft {
                background: rgba(255,255,255,0.05);
                border: 1px solid rgba(255,255,255,0.15);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }

            .floating-glass-nav {
                background: rgba(9, 26, 58, 0.26);
                border: 1px solid rgba(255,255,255,0.14);
                box-shadow: 0 10px 30px rgba(9, 26, 58, 0.18);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
            }

            .soft-card {
                background: rgba(255,255,255,0.78);
                border: 1px solid rgba(148,163,184,0.18);
                box-shadow: 0 16px 35px rgba(15,23,42,0.05);
                transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            }

            .soft-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 45px rgba(15,23,42,0.08);
                border-color: rgba(37,99,235,0.22);
            }

            .reveal {
                opacity: 0;
                transform: translateY(18px);
                transition: opacity 0.6s ease, transform 0.6s ease;
            }

            .reveal.is-visible {
                opacity: 1;
                transform: translateY(0);
            }

            .inline-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                border-radius: 9999px;
                padding: 0.45rem 0.9rem;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.14);
                color: rgba(255,255,255,0.82);
            }

            .section-kicker {
                letter-spacing: 0.18em;
                font-size: 0.68rem;
                font-weight: 700;
                text-transform: uppercase;
                color: #1d4ed8;
            }

            .section-title {
                letter-spacing: -0.05em;
                line-height: 1.1;
            }

            .section-copy {
                color: #53627a;
                line-height: 1.7;
            }

            .arrival-form {
                border: 1px solid rgba(148, 163, 184, 0.24);
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            }

            .form-input {
                min-height: 3rem;
                background: rgba(248, 250, 252, 0.72);
                transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            }

            .form-input:focus {
                background: #fff;
                box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.08);
            }

            .visit-choice {
                min-height: 3.25rem;
                border-color: rgba(148, 163, 184, 0.3);
                background: rgba(248, 250, 252, 0.68);
                transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
            }

            .visit-choice:hover {
                transform: translateY(-1px);
                border-color: rgba(29, 78, 216, 0.32);
                background: #fff;
            }

            .visit-choice:has(input:checked) {
                border-color: rgba(29, 78, 216, 0.58);
                background: rgba(29, 78, 216, 0.06);
                box-shadow: 0 8px 18px rgba(29, 78, 216, 0.08);
            }

            .camera-shell {
                border-color: rgba(148, 163, 184, 0.32);
                box-shadow: inset 0 0 0 5px rgba(248, 250, 252, 0.7), 0 12px 28px rgba(15, 23, 42, 0.06);
            }

            .service-media {
                position: relative;
                overflow: hidden;
            }

            .service-media img {
                transition: transform 0.45s ease;
            }

            .service-icon {
                position: absolute;
                top: 1rem;
                right: 1rem;
                display: inline-flex;
                width: 2.75rem;
                height: 2.75rem;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(255, 255, 255, 0.32);
                border-radius: 0.9rem;
                color: #fff;
                background: rgba(9, 26, 58, 0.54);
                box-shadow: 0 10px 22px rgba(9, 26, 58, 0.2);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                transition: transform 0.35s ease, background-color 0.35s ease, box-shadow 0.35s ease;
            }

            .soft-card:hover .service-media img {
                transform: scale(1.035);
            }

            .soft-card:hover .service-icon {
                transform: translateY(-3px) rotate(6deg) scale(1.06);
                background: rgba(29, 78, 216, 0.82);
                box-shadow: 0 14px 28px rgba(9, 26, 58, 0.28);
            }

            .nav-btn,
            .hero-cta,
            .hero-ghost,
            .primary-btn,
            .secondary-btn {
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
            }

            .nav-btn:hover,
            .hero-cta:hover,
            .hero-ghost:hover,
            .primary-btn:hover,
            .secondary-btn:hover {
                transform: translateY(-1px);
            }

            .hero-action-button,
            .primary-action,
            .secondary-action,
            .ghost-action {
                border-radius: 0.9rem;
                font-weight: 600;
                letter-spacing: -0.01em;
            }

            @media (max-width: 640px) {
                .mobile-stack {
                    display: flex;
                    flex-direction: column;
                    width: 100%;
                }

                .mobile-stack > * {
                    width: 100%;
                }

                .nav-shell {
                    gap: 0.75rem;
                }

                .nav-shell > a:last-child {
                    width: 100%;
                    text-align: center;
                }
            }

            .primary-btn:active,
            .secondary-btn:active,
            .hero-cta:active,
            .hero-ghost:active {
                transform: translateY(0) scale(0.99);
            }

            @keyframes float {
                0% {
                    transform: translate3d(0, 0, 0) scale(1);
                }
                100% {
                    transform: translate3d(18px, -20px, 0) scale(1.05);
                }
            }

            @keyframes hero-hexagon-float {
                0%, 100% {
                    transform: translate3d(0, 0, 0) rotate(0deg) scale(1);
                }
                50% {
                    transform: translate3d(-18px, 22px, 0) rotate(8deg) scale(1.04);
                }
            }

            @keyframes success-modal-in {
                from {
                    opacity: 0;
                    transform: translateY(16px) scale(0.97);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            @keyframes success-check-pop {
                from {
                    opacity: 0;
                    transform: scale(0.7);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            @keyframes success-check-draw {
                to {
                    stroke-dashoffset: 0;
                }
            }

            @keyframes badge-link-in {
                from {
                    opacity: 0;
                    transform: translateY(8px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes submit-spin {
                to {
                    transform: rotate(360deg);
                }
            }

            @media (prefers-reduced-motion: reduce) {
                html { scroll-behavior: auto; }
                *, *::before, *::after {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                }
                .reveal {
                    opacity: 1;
                    transform: none;
                }
                .hero-hexagon {
                    animation: none;
                }
                .success-modal-card,
                .success-check,
                .success-check path,
                .badge-link-entrance {
                    animation: none;
                }
            }
        </style>
    </head>
    <body data-lenis x-data="{
        formOpen: false,
        openForm() {
            this.formOpen = true;
            this.$nextTick(() => setTimeout(() => document.getElementById('formulir')?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80));
        }
    }" @open-form.window="openForm()" class="font-sans antialiased min-h-screen text-bank-navy relative overflow-x-hidden">

        {{-- ============ NAVBAR (over hero) ============ --}}
        <header class="relative z-40 h-0">
            <nav class="floating-glass-nav nav-shell fixed inset-x-0 top-4 mx-auto flex max-w-6xl items-center justify-between gap-3 rounded-2xl px-5 py-3.5">
                <a href="#" class="flex items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/25">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7v10.5a.5.5 0 0 0 .8.4L12 15l6.2 4.9a.5.5 0 0 0 .8-.4V9a7 7 0 0 0-7-7zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                    </span>
                    <span class="leading-tight text-white">
                        <span class="block text-sm font-bold tracking-widest">PELINDO</span>
                        <span class="block text-[10px] text-white/70">Regional 1 Dumai</span>
                    </span>
                </a>
                <nav class="hidden items-center gap-8 text-sm font-medium text-white/75 md:flex">
                    <a href="#layanan" class="transition-colors hover:text-white">Layanan</a>
                    <a href="#tentang" class="transition-colors hover:text-white">Tentang</a>
                    <a href="#faq" class="transition-colors hover:text-white">FAQ</a>
                    <a href="https://pelindo.id/port/pelabuhan-dumai" target="_blank" rel="noopener" class="transition-colors hover:text-white">Website Resmi</a>
                </nav>
                <a href="#formulir" @click.prevent="openForm()" class="nav-btn rounded-xl bg-bank-blue px-4 py-2 text-sm font-semibold text-white shadow-[0_10px_26px_rgba(29,78,216,0.32)] transition-colors hover:bg-indigo-700">Isi Formulir</a>
            </nav>
        </header>

        {{-- ============ HERO (foto + overlay navy, ala Brainwave) ============ --}}
        <section class="relative flex min-h-[92vh] items-center justify-center overflow-hidden bg-bank-navy">
            <span class="hero-orb one"></span>
            <span class="hero-orb two"></span>
            <div class="hero-hexagon" aria-hidden="true">
                <svg viewBox="0 0 128 128" role="presentation">
                    <polygon points="64 128 8.574 96 8.574 32 64 0 119.426 32 119.426 96" fill="currentColor" />
                </svg>
            </div>
            {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/hero.jpg --}}
            <img src="{{ asset('images/landing/hero.jpg') }}" alt="Pelabuhan Dumai" class="absolute inset-0 h-full w-full object-cover opacity-40" onerror="this.remove()">
            <div class="absolute inset-0 bg-gradient-to-b from-bank-navy/75 via-bank-navy/55 to-bank-bg"></div>

            <div class="relative z-10 mx-auto max-w-3xl px-5 pb-24 pt-28 text-center">
                <div class="reveal mb-6 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs text-white/80 ring-1 ring-white/20">
                    Kantor PT. Pelindo Regional 1 Dumai — Buku Tamu Digital
                </div>
                <h1 class="reveal text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl">Selamat Datang di<br class="hidden sm:block"> Pelabuhan Dumai</h1>
                <p class="reveal mx-auto mt-4 max-w-xl text-sm leading-relaxed text-white/70 sm:text-base">
                    PT. Pelindo (Persero) Regional 1 Dumai melayani kegiatan kepelabuhanan, logistik, dan industri di wilayah Dumai, Riau.
                    Daftarkan kunjungan Anda secara digital — resepsionis kami siap memverifikasi kehadiran Anda.
                </p>

                <div class="reveal mobile-stack mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="#formulir" @click.prevent="openForm()" class="hero-action-button hero-cta rounded-xl bg-bank-blue px-6 py-3 text-sm font-semibold text-white shadow-[0_16px_35px_rgba(29,78,216,0.32)] hover:bg-indigo-700">Isi Formulir Kunjungan</a>
                    <a href="#tentang" class="hero-action-button hero-ghost rounded-xl bg-white/10 px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/30 backdrop-blur transition-colors hover:bg-white/20">Kenali Pelabuhan Kami</a>
                </div>

                {{-- Alur singkat --}}
                <div class="reveal mt-10 flex flex-wrap items-center justify-center gap-2 text-[11px] text-white/70 sm:text-xs">
                    <span class="inline-pill">1. Isi Formulir</span>
                    <span aria-hidden="true">→</span>
                    <span class="inline-pill">2. Foto Wajah</span>
                    <span aria-hidden="true">→</span>
                    <span class="inline-pill">3. Verifikasi Resepsionis</span>
                    <span aria-hidden="true">→</span>
                    <span class="inline-pill">4. Dapat Badge QR</span>
                </div>
            </div>

            <a href="#statistik" aria-label="Gulir ke bawah" class="absolute bottom-6 left-1/2 z-10 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full bg-white/10 text-white ring-1 ring-white/30 transition-colors hover:bg-white/20">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </a>
        </section>

        {{-- ============ STATISTIK SINGKAT (strip ala Brainwave) ============ --}}
        <section id="statistik" class="border-b border-bank-light bg-white/90">
            <div class="mx-auto grid max-w-5xl grid-cols-2 gap-4 px-5 py-10 text-center sm:gap-5 md:grid-cols-4 md:py-12">
                <div class="reveal soft-card rounded-2xl p-4">
                    <p class="text-2xl font-bold text-bank-navy sm:text-3xl">1971</p>
                    <p class="mt-2 text-[11px] leading-relaxed text-bank-gray sm:text-xs">Tahun pelabuhan Dumai mulai beroperasi</p>
                </div>
                <div class="reveal soft-card rounded-2xl p-4" style="transition-delay: 80ms;">
                    <p class="text-2xl font-bold text-bank-navy sm:text-3xl">3</p>
                    <p class="mt-2 text-[11px] leading-relaxed text-bank-gray sm:text-xs">Terminal utama: peti kemas, curah cair &amp; penumpang</p>
                </div>
                <div class="reveal soft-card rounded-2xl p-4" style="transition-delay: 160ms;">
                    <p class="text-2xl font-bold text-bank-blue sm:text-3xl">24/7</p>
                    <p class="mt-2 text-[11px] leading-relaxed text-bank-gray sm:text-xs">Operasional layanan pelabuhan nonstop</p>
                </div>
                <div class="reveal soft-card rounded-2xl p-4" style="transition-delay: 240ms;">
                    <p class="text-xl font-bold text-bank-navy sm:text-2xl">Dumai–Malaka</p>
                    <p class="mt-2 text-[11px] leading-relaxed text-bank-gray sm:text-xs">Rute kapal penumpang internasional</p>
                </div>
            </div>
        </section>
{{-- ============ LAYANAN & FASILITAS (ala "Popular locations") ============ --}}
        <section id="layanan" class="scroll-mt-16 bg-bank-bg py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-5 text-center">
                <p class="section-kicker">Layanan</p>
                <h2 class="section-title mt-3 text-2xl font-bold text-bank-navy sm:text-3xl">Layanan &amp; Fasilitas Pelabuhan</h2>
                <p class="section-copy mx-auto mt-3 max-w-xl text-sm">
                    Pelabuhan Dumai melayani kegiatan kepelabuhanan, logistik, dan industri — mendukung ekspor CPO serta konektivitas antarnegara.
                </p>

                <div class="mt-10 grid gap-6 sm:grid-cols-3">
                    {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/service-1.jpg --}}
                    <div class="reveal soft-card overflow-hidden rounded-2xl">
                        <div class="service-media">
                            <img src="{{ asset('images/landing/service-1.jpg') }}" alt="Terminal Peti Kemas" class="h-56 w-full bg-bank-light object-cover sm:h-72" onerror="this.remove()">
                            <span class="service-icon" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8-4 8 4m-16 0 8 4 8-4M4 7v10l8 4 8-4V7m-8 4v10" /></svg>
                            </span>
                        </div>
                        <div class="p-5 text-left">
                            <h3 class="font-semibold text-bank-navy">Peti Kemas &amp; Logistik</h3>
                            <p class="mt-1 text-xs text-bank-gray">Terminal kontainer untuk ekspor-impor wilayah Riau dan sekitarnya.</p>
                        </div>
                    </div>
                    {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/service-2.jpg --}}
                    <div class="reveal soft-card overflow-hidden rounded-2xl" style="transition-delay: 120ms;">
                        <div class="service-media">
                            <img src="{{ asset('images/landing/service-2.jpg') }}" alt="Curah Cair dan CPO" class="h-56 w-full bg-bank-light object-cover sm:h-72" onerror="this.remove()">
                            <span class="service-icon" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5c3.2 3.7 5 6.5 5 9.2a5 5 0 0 1-10 0c0-2.7 1.8-5.5 5-9.2Z" /></svg>
                            </span>
                        </div>
                        <div class="p-5 text-left">
                            <h3 class="font-semibold text-bank-navy">Curah Cair (CPO) &amp; Tangki</h3>
                            <p class="mt-1 text-xs text-bank-gray">Terminal curah cair dan fasilitas penyangga minyak sawit untuk ekspor.</p>
                        </div>
                    </div>
                    {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/service-3.jpg --}}
                    <div class="reveal soft-card overflow-hidden rounded-2xl" style="transition-delay: 240ms;">
                        <div class="service-media">
                            <img src="{{ asset('images/landing/service-3.jpg') }}" alt="Penumpang dan Feri Dumai Malaka" class="h-56 w-full bg-bank-light object-cover sm:h-72" onerror="this.remove()">
                            <span class="service-icon" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5h18M5 16.5l1.5-5h8l3 5M8 11.5l2-3h4l2.5 3M6 19.5h.01M18 19.5h.01" /></svg>
                            </span>
                        </div>
                        <div class="p-5 text-left">
                            <h3 class="font-semibold text-bank-navy">Penumpang &amp; Feri Dumai–Malaka</h3>
                            <p class="mt-1 text-xs text-bank-gray">Terminal penumpang dengan layanan feri internasional rute Dumai–Malaka.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

{{-- ============ TENTANG PELABUHAN (ala "Work around talented people") ============ --}}
        <section id="tentang" class="scroll-mt-16 bg-white py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-5">
                <div class="grid items-center gap-10 lg:grid-cols-2">
                    <div>
                        <p class="section-kicker">Tentang</p>
                        <h2 class="section-title mt-3 max-w-md text-2xl font-bold text-bank-navy sm:text-3xl">Gerbang logistik dan industri di kota Dumai.</h2>
                        <p class="section-copy mt-4 max-w-md text-sm">
                            Pelabuhan Dumai berdiri sejak 1971 dan kini menjadi salah satu pintu keluar utama ekspor CPO serta
                            komoditas Indonesia, sekaligus melayani lintas batas Dumai–Malaka. Buku tamu digital ini menjaga
                            setiap kunjungan Anda tercatat aman dan terkendali sesuai standar keamanan pelabuhan internasional.
                        </p>
                    </div>
                    {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/about-1.jpg --}}
                    <img src="{{ asset('images/landing/about-1.jpg') }}" alt="Suasana Pelabuhan Dumai" class="h-64 w-full rounded-2xl bg-bank-light object-cover shadow-sm sm:h-80 lg:ml-auto lg:max-w-md" onerror="this.remove()">
                </div>

                <div class="mt-8 grid items-center gap-10 lg:grid-cols-2">
                    {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/about-2.jpg --}}
                    <img src="{{ asset('images/landing/about-2.jpg') }}" alt="Aktivitas terminal Pelabuhan Dumai" class="h-56 w-full rounded-2xl bg-bank-light object-cover shadow-sm sm:h-72 lg:order-2 lg:max-w-md" onerror="this.remove()">
                    <div class="lg:order-1 lg:pl-6">
                        <p class="max-w-md text-sm leading-relaxed text-bank-gray">
                            Dengan sistem kunjungan digital, tamu tidak lagi mengisi buku tamu manual. Cukup beberapa langkah
                            di kiosk ini, identitas dan foto Anda langsung terverifikasi oleh resepsionis dan badge QR kunjungan
                            terbit seketika.
                        </p>
                    </div>
                </div>

                {{-- Fitur dengan glass icon --}}
                <div class="mt-12 grid gap-6 sm:grid-cols-3">
                    <div class="reveal soft-card flex items-start gap-4 rounded-2xl p-5">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 8.5A2.5 2.5 0 0 1 6.5 6h11A2.5 2.5 0 0 1 20 8.5v7A2.5 2.5 0 0 1 17.5 18h-11A2.5 2.5 0 0 1 4 15.5v-7Z"/><path d="M7 9.5h10M9 15h6" stroke-linecap="round"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-bank-navy">Check-in Digital</h3>
                            <p class="mt-1 text-xs leading-relaxed text-bank-gray">Formulir kedatangan dan foto wajah langsung dari kiosk — tanpa kertas.</p>
                        </div>
                    </div>
                    <div class="reveal soft-card flex items-start gap-4 rounded-2xl p-5" style="transition-delay: 120ms;">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 7h10v10H7z"/><path d="M9.5 9.5h5v5h-5z"/><path d="M4 12h3M17 12h3M12 4v3M12 17v3" stroke-linecap="round"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-bank-navy">Verifikasi QR</h3>
                            <p class="mt-1 text-xs leading-relaxed text-bank-gray">Setiap kunjungan menghasilkan badge QR yang diverifikasi resepsionis.</p>
                        </div>
                    </div>
                    <div class="reveal soft-card flex items-start gap-4 rounded-2xl p-5" style="transition-delay: 240ms;">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3.5 18 6v5.5c0 4.1-2.7 7.7-6 9.5-3.3-1.8-6-5.4-6-9.5V6l6-2.5Z"/><path d="m9.5 12 1.5 1.5 3.5-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-bank-navy">Keamanan Standar ISPS</h3>
                            <p class="mt-1 text-xs leading-relaxed text-bank-gray">Pencatatan tamu sesuai kode keamanan pelabuhan internasional (ISPS Code).</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

{{-- ============ FAQ (section navy ala Brainwave) ============ --}}
        <section id="faq" class="scroll-mt-16 bg-bank-navy py-16 sm:py-20">
            <div class="mx-auto grid max-w-6xl items-start gap-12 px-5 lg:grid-cols-2">
                <div>
                    <p class="section-kicker text-white/85">FAQ</p>
                    <h2 class="section-title mt-3 text-2xl font-bold leading-tight text-white sm:text-3xl">Kami selalu siap<br>mendampingi kunjungan Anda.</h2>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/60">
                        Petugas resepsionis dan keamanan pelabuhan standby setiap hari untuk memastikan kunjungan Anda lancar dan aman.
                    </p>
                    <ul class="mt-8 space-y-6">
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-bank-green">
                                <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-semibold text-white">Resepsionis 24/7</h3>
                                <p class="mt-1 max-w-xs text-xs leading-relaxed text-white/60">Kunjungan bisa didaftarkan kapan pun — pelabuhan beroperasi nonstop.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-bank-green">
                                <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-semibold text-white">Check-in di Bawah 2 Menit</h3>
                                <p class="mt-1 max-w-xs text-xs leading-relaxed text-white/60">Cukup isi data, ambil foto, dan badge QR Anda terbit seketika.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="rounded-[24px] bg-white p-3 shadow-[0_18px_45px_rgba(9,26,58,0.14)] ring-1 ring-bank-light/70">
                    <div x-data="{ open: { 0: true, 1: false, 2: false, 3: false }, toggle(index) { this.open[index] = !this.open[index] } }" class="divide-y divide-bank-light">
                        <div class="rounded-xl px-4 py-1" :class="open[0] ? 'bg-bank-bg' : ''">
                            <button type="button" @click="toggle(0)" class="flex w-full items-center justify-between py-3.5 text-left text-sm font-semibold text-bank-navy">
                                Dokumen apa yang perlu saya bawa saat berkunjung?
                                <svg class="h-4 w-4 shrink-0 text-bank-gray transition-transform duration-200" :class="open[0] ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open[0]" x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="max-h-0 opacity-0 -translate-y-1" x-transition:enter-end="max-h-40 opacity-100 translate-y-0" x-transition:leave="transition-all ease-in duration-[250ms]" x-transition:leave-start="max-h-40 opacity-100 translate-y-0" x-transition:leave-end="max-h-0 opacity-0 -translate-y-1" x-cloak class="overflow-hidden pb-4 text-xs leading-relaxed text-bank-gray">
                                Bawa identitas diri resmi (KTP/ SIM/ Paspor). Sebutkan pegawai yang dituju pada formulir agar resepsionis dapat memverifikasi kunjungan Anda dengan cepat.
                            </div>
                        </div>
                        <div class="rounded-xl px-4 py-1" :class="open[1] ? 'bg-bank-bg' : ''">
                            <button type="button" @click="toggle(1)" class="flex w-full items-center justify-between py-3.5 text-left text-sm font-semibold text-bank-navy">
                                Bagaimana cara mendapatkan badge QR kunjungan?
                                <svg class="h-4 w-4 shrink-0 text-bank-gray transition-transform duration-200" :class="open[1] ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open[1]" x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="max-h-0 opacity-0 -translate-y-1" x-transition:enter-end="max-h-40 opacity-100 translate-y-0" x-transition:leave="transition-all ease-in duration-[250ms]" x-transition:leave-start="max-h-40 opacity-100 translate-y-0" x-transition:leave-end="max-h-0 opacity-0 -translate-y-1" x-cloak class="overflow-hidden pb-4 text-xs leading-relaxed text-bank-gray">
                                Setelah formulir terkirim dan foto Anda terekam, sistem langsung menerbitkan badge QR. Tunjukkan badge tersebut kepada resepsionis untuk diverifikasi sebelum masuk area kantor.
                            </div>
                        </div>
                        <div class="rounded-xl px-4 py-1" :class="open[2] ? 'bg-bank-bg' : ''">
                            <button type="button" @click="toggle(2)" class="flex w-full items-center justify-between py-3.5 text-left text-sm font-semibold text-bank-navy">
                                Apakah saya bisa berkunjung tanpa janji temu?
                                <svg class="h-4 w-4 shrink-0 text-bank-gray transition-transform duration-200" :class="open[2] ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open[2]" x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="max-h-0 opacity-0 -translate-y-1" x-transition:enter-end="max-h-40 opacity-100 translate-y-0" x-transition:leave="transition-all ease-in duration-[250ms]" x-transition:leave-start="max-h-40 opacity-100 translate-y-0" x-transition:leave-end="max-h-0 opacity-0 -translate-y-1" x-cloak class="overflow-hidden pb-4 text-xs leading-relaxed text-bank-gray">
                                Bisa. Namun kami menyarankan menghubungi pegawai yang dituju terlebih dahulu agar kunjungan Anda lebih cepat diproses oleh resepsionis.
                            </div>
                        </div>
                        <div class="rounded-xl px-4 py-1" :class="open[3] ? 'bg-bank-bg' : ''">
                            <button type="button" @click="toggle(3)" class="flex w-full items-center justify-between py-3.5 text-left text-sm font-semibold text-bank-navy">
                                Apa saja yang dilarang dibawa ke area pelabuhan?
                                <svg class="h-4 w-4 shrink-0 text-bank-gray transition-transform duration-200" :class="open[3] ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open[3]" x-transition:enter="transition-all ease-out duration-500" x-transition:enter-start="max-h-0 opacity-0 -translate-y-1" x-transition:enter-end="max-h-40 opacity-100 translate-y-0" x-transition:leave="transition-all ease-in duration-[250ms]" x-transition:leave-start="max-h-40 opacity-100 translate-y-0" x-transition:leave-end="max-h-0 opacity-0 -translate-y-1" x-cloak class="overflow-hidden pb-4 text-xs leading-relaxed text-bank-gray">
                                Sesuai ketentuan keamanan ISPS, senjata, bahan mudah terbakar, dan barang berbahaya lainnya dilarang dibawa ke area terminal. Petugas keamanan berhak melakukan pemeriksaan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

{{-- ============ CTA + FORM (form muncul setelah tombol diklik) ============ --}}
        <div>
        <section class="bg-bank-bg px-5 py-16 text-center">
            <div class="mx-auto max-w-2xl rounded-[28px] border border-bank-light bg-white/90 px-6 py-8 shadow-[0_18px_45px_rgba(15,23,42,0.05)] sm:px-8 sm:py-10">
                <span class="inline-flex items-center gap-2 rounded-full bg-bank-blue/5 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.18em] text-bank-blue">Ready to visit</span>
                <h2 class="mt-4 text-2xl font-bold text-bank-navy sm:text-3xl">Siap melakukan kunjungan?</h2>
                <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-bank-gray">
                    Lengkapi formulir kedatangan sekarang — prosesnya cepat, hanya beberapa langkah di layar kiosk ini.
                </p>
                <button type="button" @click="openForm()" class="hero-cta mt-6 inline-block rounded-xl bg-bank-blue px-8 py-3 text-sm font-semibold text-white shadow-[0_14px_30px_rgba(29,78,216,0.28)] transition-colors hover:bg-indigo-700">Isi Formulir Kedatangan</button>
            </div>
        </section>

        {{-- ============ FORM KIOSK ============ --}}
        <div id="formulir" x-show="formOpen" x-cloak class="mx-auto max-w-3xl scroll-mt-8 px-4 pb-16 pt-4">
            <form id="kiosk-form" class="arrival-form overflow-hidden rounded-3xl bg-white">
                @csrf
                <div class="space-y-5 px-5 py-5 sm:p-8">
                    <div class="flex items-center justify-between gap-3 border-b border-bank-light pb-4">
                        <h2 class="text-lg font-semibold text-bank-navy">Formulir Kedatangan</h2>
                        <span class="rounded-full bg-bank-blue/5 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-bank-blue">Kiosk</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-bank-navy mb-1">Nama Lengkap</label>
                        <input type="text" name="visitor_name" required class="form-input w-full rounded-xl border border-bank-light px-3 py-2.5 text-sm text-bank-navy placeholder-bank-gray focus:border-bank-blue focus:ring-0 focus:outline-none" placeholder="Nama Anda">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-bank-navy mb-1">No. Telepon</label>
                            <input type="text" name="visitor_phone" required class="form-input w-full rounded-xl border border-bank-light px-3 py-2.5 text-sm text-bank-navy placeholder-bank-gray focus:border-bank-blue focus:ring-0 focus:outline-none" placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-bank-navy mb-1">Instansi / Perusahaan</label>
                            <input type="text" name="visitor_institution" required class="form-input w-full rounded-xl border border-bank-light px-3 py-2.5 text-sm text-bank-navy placeholder-bank-gray focus:border-bank-blue focus:ring-0 focus:outline-none" placeholder="Nama instansi">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-bank-navy mb-1">Pegawai yang Dituju</label>
                        <select name="employee_id" class="form-input w-full rounded-xl border border-bank-light px-3 py-2.5 text-sm text-bank-navy bg-white focus:border-bank-blue focus:ring-0 focus:outline-none">
                            <option value="">— Pilih Pegawai —</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} — {{ $employee->department }}</option>
                            @endforeach
                        </select>
                    </div>
<div>
                        <label class="block text-sm font-medium text-bank-navy mb-2">Jenis Kunjungan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <label class="visit-choice flex items-center gap-2 rounded-xl border px-3 py-2.5 text-sm text-bank-navy cursor-pointer">
                                <input type="radio" name="visit_type" value="meet" checked class="accent-[#1814F3]" onchange="document.getElementById('delivery-options').classList.add('hidden')">
                                Menemui Pegawai
                            </label>
                            <label class="visit-choice flex items-center gap-2 rounded-xl border px-3 py-2.5 text-sm text-bank-navy cursor-pointer">
                                <input type="radio" name="visit_type" value="deliver" class="accent-[#1814F3]" onchange="document.getElementById('delivery-options').classList.remove('hidden')">
                                Mengantar Surat / Dokumen
                            </label>
                            <label class="visit-choice flex items-center gap-2 rounded-xl border px-3 py-2.5 text-sm text-bank-navy cursor-pointer">
                                <input type="radio" name="visit_type" value="meeting_invitation" class="accent-[#1814F3]" onchange="document.getElementById('delivery-options').classList.add('hidden')">
                                Undangan Rapat / Kegiatan
                            </label>
                        </div>
                        <div id="delivery-options" class="hidden mt-2 rounded-xl border border-amber-200 bg-amber-50 p-3">
                            <label class="block text-sm font-medium text-amber-800 mb-2">Surat akan diserahkan bagaimana?</label>
                            <label class="flex items-center gap-2 text-sm text-amber-900 mb-1 cursor-pointer">
                                <input type="radio" name="delivery_pref" value="hand_in" class="accent-[#1814F3]">
                                Diantar langsung ke pegawai yang dituju
                            </label>
                            <label class="flex items-center gap-2 text-sm text-amber-900 cursor-pointer">
                                <input type="radio" name="delivery_pref" value="leave" checked class="accent-[#1814F3]">
                                Dititipkan ke resepsionis yang bertanggung jawab
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-bank-navy mb-1">Keperluan Kunjungan</label>
                        <textarea name="purpose" rows="3" required class="form-input w-full rounded-xl border border-bank-light px-3 py-2.5 text-sm text-bank-navy placeholder-bank-gray focus:border-bank-blue focus:ring-0 focus:outline-none" placeholder="Tuliskan keperluan kunjungan Anda"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-bank-navy mb-2">Foto Wajah (Wajib)</label>
                        <div id="camera-box" class="camera-shell relative overflow-hidden rounded-2xl border bg-bank-bg">
                            <video id="video" autoplay playsinline class="w-full aspect-video object-cover"></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            <div id="camera-off" class="absolute inset-0 flex items-center justify-center text-sm text-bank-gray">Menginisialisasi kamera...</div>
                            <div id="capture-overlay" class="hidden absolute inset-0 bg-black/60 flex items-center justify-center">
                                <img id="captured" class="max-h-full" alt="captured">
                            </div>
                        </div>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                            <button type="button" id="snap-btn" class="primary-action rounded-xl bg-bank-blue px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 disabled:opacity-50 transition-colors">Ambil Foto</button>
                            <button type="button" id="switch-camera-btn" class="secondary-action rounded-xl bg-white px-4 py-2.5 text-sm font-medium text-bank-navy ring-1 ring-bank-light hover:bg-bank-bg transition-colors">Switch Kamera</button>
                            <button type="button" id="retake-btn" class="ghost-action hidden rounded-xl bg-bank-light px-4 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/70 transition-colors">Ulangi</button>
                        </div>
                        <input type="hidden" id="photo" name="photo">
                    </div>

                    <button type="submit" id="submit-btn" class="primary-action w-full rounded-xl bg-bank-blue py-3 text-white font-semibold shadow-[0_14px_30px_rgba(29,78,216,0.28)] hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                        <span id="submit-label">Kirim Kunjungan</span>
                        <span id="submit-spinner" class="submit-spinner ml-2 hidden" aria-hidden="true"></span>
                    </button>
                    <p id="form-error" class="hidden text-sm text-bank-red"></p>
                </div>
            </form>
<!-- Success modal -->
            <div id="success-modal" class="hidden fixed inset-0 bg-bank-navy/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="success-modal-card bg-white text-bank-navy rounded-3xl max-w-md w-full p-6 text-center shadow-xl">
                    <div id="blacklist-alert" class="hidden mb-4 rounded-xl bg-red-50 border border-red-200 text-bank-red px-4 py-3 text-sm">
                        ⚠️ Nama anda terdeteksi dalam daftar hitam. Mohon menunggu dan segera laporkan diri kepada resepsionis.
                    </div>
                    <div class="success-check mx-auto h-14 w-14 rounded-full bg-bank-bg flex items-center justify-center mb-3">
                        <svg class="h-8 w-8 text-bank-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-bank-navy">Kunjungan Terkirim</h2>
                    <p id="success-msg" class="mt-2 text-sm text-bank-gray"></p>
                    <a id="badge-link" href="#" target="_blank" class="badge-link-entrance mt-5 inline-block rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 transition-colors">Lihat QR Badge</a>
                    <button type="button" onClick="location.reload()" class="mt-3 block w-full rounded-xl bg-bank-light px-5 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/70 transition-colors">Selesai</button>
                </div>
            </div>
        </div>
        </div><!-- /wrapper CTA + form -->

        {{-- ============ FOOTER ============ --}}
        <div id="toast-container" class="toast-container"></div>

        <footer class="bg-bank-navy text-white/70">
            <div class="mx-auto grid max-w-6xl gap-10 px-5 py-12 md:grid-cols-4">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/25">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7v10.5a.5.5 0 0 0 .8.4L12 15l6.2 4.9a.5.5 0 0 0 .8-.4V9a7 7 0 0 0-7-7zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        </span>
                        <span class="leading-tight text-white">
                            <span class="block text-sm font-bold tracking-widest">PELINDO</span>
                            <span class="block text-[10px] text-white/70">Regional 1 Dumai</span>
                        </span>
                    </div>
                    <p class="mt-4 max-w-sm text-xs leading-relaxed text-white/60">
                        PT. Pelabuhan Indonesia (Persero) — Regional 1 Dumai. Melayani kegiatan kepelabuhanan,
                        logistik, dan industri di Pelabuhan Dumai, Riau, sejak 1971.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">Navigasi</h3>
                    <ul class="mt-4 space-y-2.5 text-xs">
                        <li><a href="#layanan" class="transition-colors hover:text-white">Layanan &amp; Fasilitas</a></li>
                        <li><a href="#tentang" class="transition-colors hover:text-white">Tentang Pelabuhan</a></li>
                        <li><a href="#faq" class="transition-colors hover:text-white">FAQ</a></li>
                        <li><a href="#formulir" @click.prevent="openForm()" class="transition-colors hover:text-white">Formulir Kunjungan</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">Kontak &amp; Tautan</h3>
                    <ul class="mt-4 space-y-2.5 text-xs">
                        <li>Pelabuhan Dumai, Kota Dumai, Riau</li>
                        <li><a href="https://pelindo.id/port/pelabuhan-dumai" target="_blank" rel="noopener" class="font-medium text-white underline-offset-2 transition-colors hover:underline">pelindo.id/port/pelabuhan-dumai</a></li>
                        <li><a href="https://pelindo.id" target="_blank" rel="noopener" class="transition-colors hover:text-white">pelindo.id</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 py-5 text-center text-[11px] text-white/50">
                © {{ date('Y') }} PT. Pelabuhan Indonesia (Persero) — Regional 1 Dumai. Seluruh hak cipta dilindungi.
            </div>
        </footer>

        <script>
        (function () {
            const revealItems = document.querySelectorAll('.reveal');
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12 });

                revealItems.forEach((item) => observer.observe(item));
            } else {
                revealItems.forEach((item) => item.classList.add('is-visible'));
            }

            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const camOff = document.getElementById('camera-off');
            const snapBtn = document.getElementById('snap-btn');
            const switchCameraBtn = document.getElementById('switch-camera-btn');
            const retakeBtn = document.getElementById('retake-btn');
            const captured = document.getElementById('captured');
            const captureOverlay = document.getElementById('capture-overlay');
            const photoInput = document.getElementById('photo');
            const form = document.getElementById('kiosk-form');
            const submitBtn = document.getElementById('submit-btn');
            const submitLabel = document.getElementById('submit-label');
            const submitSpinner = document.getElementById('submit-spinner');
            const formError = document.getElementById('form-error');
            const successModal = document.getElementById('success-modal');
            const successMsg = document.getElementById('success-msg');
            const badgeLink = document.getElementById('badge-link');
            const blacklistAlert = document.getElementById('blacklist-alert');

            let stream = null;
            let currentFacingMode = 'user';

            function setMirror(el) {
                if (currentFacingMode === 'user') el.classList.add('mirror-y');
                else el.classList.remove('mirror-y');
            }

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(function (track) { track.stop(); });
                    stream = null;
                }
            }

            async function startCamera() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    camOff.textContent = 'Kamera tidak dapat diakses. Pastikan browser memberi izin kamera (https/localhost).';
                    camOff.classList.remove('hidden');
                    camOff.classList.add('text-red-600', 'font-medium');
                    snapBtn.disabled = true;
                    switchCameraBtn.disabled = true;
                    return;
                }

                try {
                    stopCamera();
                    camOff.textContent = 'Menginisialisasi kamera...';
                    camOff.classList.remove('hidden');
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: currentFacingMode } } });
                    video.srcObject = stream;
                    setMirror(video);
                    camOff.classList.add('hidden');
                    snapBtn.disabled = false;
                    switchCameraBtn.disabled = false;
                } catch (e) {
                    camOff.textContent = 'Kamera tidak dapat diakses. Pastikan browser memberi izin kamera (https/localhost).';
                    camOff.classList.remove('hidden');
                    camOff.classList.add('text-red-600', 'font-medium');
                    snapBtn.disabled = true;
                }
            }
            startCamera();

            switchCameraBtn.addEventListener('click', function () {
                currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
                photoInput.value = '';
                captured.src = '';
                captureOverlay.classList.add('hidden');
                retakeBtn.classList.add('hidden');
                snapBtn.classList.remove('hidden');
                startCamera();
            });

            snapBtn.addEventListener('click', function () {
                if (!video.videoWidth) return;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                var ctx = canvas.getContext('2d');
                // Jika MIRROR, balik gambar agar hasil foto sesuai mode pratinjau.
                if (currentFacingMode === 'user') { ctx.translate(canvas.width, 0); ctx.scale(-1, 1); }
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                photoInput.value = dataUrl;
                captured.src = dataUrl;
                captureOverlay.classList.remove('hidden');
                snapBtn.classList.add('hidden');
                retakeBtn.classList.remove('hidden');
                stopCamera();
            });

            retakeBtn.addEventListener('click', function () {
                photoInput.value = '';
                captured.src = '';
                captureOverlay.classList.add('hidden');
                retakeBtn.classList.add('hidden');
                snapBtn.classList.remove('hidden');
                startCamera();
            });

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                formError.classList.add('hidden');
                if (!photoInput.value) {
                    formError.textContent = 'Silakan ambil foto wajah terlebih dahulu.';
                    formError.classList.remove('hidden');
                    return;
                }
                submitBtn.disabled = true;
                submitBtn.setAttribute('aria-busy', 'true');
                submitLabel.textContent = 'Mengirim...';
                submitSpinner.classList.remove('hidden');

                const formData = new FormData(form);
                try {
                    const res = await fetch('{{ route("kiosk.store") }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || 'Terjadi kesalahan.');
                    }

                    window.BukuTamuToast?.show({
                        title: 'Kunjungan terkirim',
                        description: data.message,
                        variant: 'success',
                        duration: 3200,
                    });

                    successMsg.textContent = data.message;
                    badgeLink.href = '{{ url("badge") }}' + '/' + data.visit.qr_code_token;
                    if (data.blacklisted) blacklistAlert.classList.remove('hidden');
                    else blacklistAlert.classList.add('hidden');
                    successModal.classList.remove('hidden');
                    form.reset();
                    photoInput.value = '';
                    captureOverlay.classList.add('hidden');
                    retakeBtn.classList.add('hidden');
                    snapBtn.classList.remove('hidden');
                } catch (err) {
                    formError.textContent = err.message;
                    formError.classList.remove('hidden');
                    window.BukuTamuToast?.show({
                        title: 'Gagal mengirim',
                        description: err.message,
                        variant: 'error',
                        duration: 4200,
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.removeAttribute('aria-busy');
                    submitLabel.textContent = 'Kirim Kunjungan';
                    submitSpinner.classList.add('hidden');
                }
            });
        })();
        </script>
    </body>
</html>
