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
            /* Non-mirror (default): tampil apa adanya (sesuai sensor kamera). */
            .mirror-y { transform: scaleX(-1); }
            /* Smooth scroll untuk navigasi anchor landing (#formulir, #layanan, dst). */
            html { scroll-behavior: smooth; }
            /* Sembunyikan konten Alpine sebelum inisialisasi (anti-flash). */
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body data-lenis x-data="{ formOpen: false }" @open-form.window="formOpen = true; $nextTick(() => setTimeout(() => document.getElementById('formulir')?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80))" class="font-sans antialiased bg-bank-bg min-h-screen text-bank-navy relative overflow-x-hidden">

        {{-- ============ NAVBAR (over hero) ============ --}}
        <header class="absolute inset-x-0 top-0 z-30">
            <nav class="mx-auto flex max-w-6xl items-center justify-between px-5 py-5">
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
                    <a href="https://pelindo.id/port/pelabuhan-dumai" target="_blank" rel="noopener" class="transition-colors hover:text-white">Website Resmi ↗</a>
                </nav>
                <a href="#formulir" @click.prevent="$dispatch('open-form')" class="rounded-xl bg-bank-blue px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-bank-blue/40 transition-colors hover:bg-indigo-700">Isi Formulir</a>
            </nav>
        </header>

        {{-- ============ HERO (foto + overlay navy, ala Brainwave) ============ --}}
        <section class="relative flex min-h-[92vh] items-center justify-center overflow-hidden bg-bank-navy">
            {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/hero.jpg --}}
            <img src="{{ asset('images/landing/hero.jpg') }}" alt="Pelabuhan Dumai" class="absolute inset-0 h-full w-full object-cover opacity-40" onerror="this.remove()">
            <div class="absolute inset-0 bg-gradient-to-b from-bank-navy/70 via-bank-navy/45 to-bank-bg"></div>

            <div class="relative z-10 mx-auto max-w-3xl px-5 pb-24 pt-28 text-center">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs text-white/80 ring-1 ring-white/20">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-bank-green"></span>
                    Kantor PT. Pelindo Regional 1 Dumai — Buku Tamu Digital
                </div>
                <h1 class="text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl">Selamat Datang di<br class="hidden sm:block"> Pelabuhan Dumai</h1>
                <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-white/70 sm:text-base">
                    PT. Pelindo (Persero) Regional 1 Dumai melayani kegiatan kepelabuhanan, logistik, dan industri di wilayah Dumai, Riau.
                    Daftarkan kunjungan Anda secara digital — resepsionis kami siap memverifikasi kehadiran Anda.
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="#formulir" @click.prevent="$dispatch('open-form')" class="rounded-xl bg-bank-blue px-6 py-3 text-sm font-semibold text-white shadow-xl shadow-bank-blue/40 transition-colors hover:bg-indigo-700">Isi Formulir Kunjungan</a>
                    <a href="#tentang" class="rounded-xl bg-white/10 px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/30 backdrop-blur transition-colors hover:bg-white/20">Kenali Pelabuhan Kami</a>
                </div>

                {{-- Alur singkat --}}
                <div class="mt-10 flex flex-wrap items-center justify-center gap-2 text-[11px] text-white/70 sm:text-xs">
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15">1. Isi Formulir</span>
                    <span aria-hidden="true">→</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15">2. Foto Wajah</span>
                    <span aria-hidden="true">→</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15">3. Verifikasi Resepsionis</span>
                    <span aria-hidden="true">→</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15">4. Dapat Badge QR</span>
                </div>
            </div>

            <a href="#statistik" aria-label="Gulir ke bawah" class="absolute bottom-6 left-1/2 z-10 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full bg-white/10 text-white ring-1 ring-white/30 transition-colors hover:bg-white/20">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </a>
        </section>

        {{-- ============ STATISTIK SINGKAT (strip ala Brainwave) ============ --}}
        <section id="statistik" class="border-b border-bank-light bg-white">
            <div class="mx-auto grid max-w-5xl grid-cols-2 gap-x-6 gap-y-10 px-6 py-12 text-center md:grid-cols-4">
                <div>
                    <p class="text-3xl font-bold text-bank-navy">1971</p>
                    <p class="mt-1.5 text-xs text-bank-gray">Tahun pelabuhan Dumai mulai beroperasi</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-bank-navy">3</p>
                    <p class="mt-1.5 text-xs text-bank-gray">Terminal utama: peti kemas, curah cair & penumpang</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-bank-blue">24/7</p>
                    <p class="mt-1.5 text-xs text-bank-gray">Operasional layanan pelabuhan nonstop</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-bank-navy">Dumai–Malaka</p>
                    <p class="mt-1.5 text-xs text-bank-gray">Rute kapal penumpang internasional</p>
                </div>
            </div>
        </section>
{{-- ============ LAYANAN & FASILITAS (ala "Popular locations") ============ --}}
        <section id="layanan" class="scroll-mt-16 bg-bank-bg py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-5 text-center">
                <h2 class="text-2xl font-bold text-bank-navy sm:text-3xl">Layanan &amp; Fasilitas Pelabuhan</h2>
                <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-bank-gray">
                    Pelabuhan Dumai melayani kegiatan kepelabuhanan, logistik, dan industri — mendukung ekspor CPO serta konektivitas antarnegara.
                </p>

                <div class="mt-10 grid gap-6 sm:grid-cols-3">
                    {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/service-1.jpg --}}
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm transition-shadow hover:shadow-lg">
                        <img src="{{ asset('images/landing/service-1.jpg') }}" alt="Terminal Peti Kemas" class="h-56 w-full bg-bank-light object-cover sm:h-72" onerror="this.remove()">
                        <div class="p-5 text-left">
                            <h3 class="font-semibold text-bank-navy">Peti Kemas &amp; Logistik</h3>
                            <p class="mt-1 text-xs text-bank-gray">Terminal kontainer untuk ekspor-impor wilayah Riau dan sekitarnya.</p>
                        </div>
                    </div>
                    {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/service-2.jpg --}}
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm transition-shadow hover:shadow-lg">
                        <img src="{{ asset('images/landing/service-2.jpg') }}" alt="Curah Cair dan CPO" class="h-56 w-full bg-bank-light object-cover sm:h-72" onerror="this.remove()">
                        <div class="p-5 text-left">
                            <h3 class="font-semibold text-bank-navy">Curah Cair (CPO) &amp; Tangki</h3>
                            <p class="mt-1 text-xs text-bank-gray">Terminal curah cair dan fasilitas penyangga minyak sawit untuk ekspor.</p>
                        </div>
                    </div>
                    {{-- Ganti gambar: simpan foto Anda sebagai public/images/landing/service-3.jpg --}}
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm transition-shadow hover:shadow-lg">
                        <img src="{{ asset('images/landing/service-3.jpg') }}" alt="Penumpang dan Feri Dumai Malaka" class="h-56 w-full bg-bank-light object-cover sm:h-72" onerror="this.remove()">
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
                        <h2 class="max-w-md text-2xl font-bold leading-snug text-bank-navy sm:text-3xl">Gerbang logistik dan industri di kota Dumai.</h2>
                        <p class="mt-4 max-w-md text-sm leading-relaxed text-bank-gray">
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
                <div class="mt-12 grid gap-10 sm:grid-cols-3">
                    <div class="flex items-start gap-4">
                        <x-glass-icon name="camera" tone="orange" size="md" />
                        <div>
                            <h3 class="text-sm font-semibold text-bank-navy">Check-in Digital</h3>
                            <p class="mt-1 text-xs leading-relaxed text-bank-gray">Formulir kedatangan dan foto wajah langsung dari kiosk — tanpa kertas.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <x-glass-icon name="qr" tone="purple" size="md" />
                        <div>
                            <h3 class="text-sm font-semibold text-bank-navy">Verifikasi QR</h3>
                            <p class="mt-1 text-xs leading-relaxed text-bank-gray">Setiap kunjungan menghasilkan badge QR yang diverifikasi resepsionis.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <x-glass-icon name="shield" tone="green" size="md" />
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
                    <h2 class="text-2xl font-bold leading-snug text-white sm:text-3xl">Kami selalu siap<br>mendampingi kunjungan Anda.</h2>
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

                <div class="rounded-2xl bg-white p-3 shadow-2xl shadow-bank-navy/40">
                    <div x-data="{ open: 0 }" class="divide-y divide-bank-light">
                        <div class="rounded-xl px-4 py-1" :class="open === 0 ? 'bg-bank-bg' : ''">
                            <button type="button" @click="open = open === 0 ? null : 0" class="flex w-full items-center justify-between py-3.5 text-left text-sm font-semibold text-bank-navy">
                                Dokumen apa yang perlu saya bawa saat berkunjung?
                                <svg class="h-4 w-4 shrink-0 text-bank-gray transition-transform" :class="open === 0 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <p x-show="open === 0" x-cloak class="pb-4 text-xs leading-relaxed text-bank-gray">
                                Bawa identitas diri resmi (KTP/ SIM/ Paspor). Sebutkan pegawai yang dituju pada formulir agar resepsionis dapat memverifikasi kunjungan Anda dengan cepat.
                            </p>
                        </div>
                        <div class="rounded-xl px-4 py-1" :class="open === 1 ? 'bg-bank-bg' : ''">
                            <button type="button" @click="open = open === 1 ? null : 1" class="flex w-full items-center justify-between py-3.5 text-left text-sm font-semibold text-bank-navy">
                                Bagaimana cara mendapatkan badge QR kunjungan?
                                <svg class="h-4 w-4 shrink-0 text-bank-gray transition-transform" :class="open === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <p x-show="open === 1" x-cloak class="pb-4 text-xs leading-relaxed text-bank-gray">
                                Setelah formulir terkirim dan foto Anda terekam, sistem langsung menerbitkan badge QR. Tunjukkan badge tersebut kepada resepsionis untuk diverifikasi sebelum masuk area kantor.
                            </p>
                        </div>
                        <div class="rounded-xl px-4 py-1" :class="open === 2 ? 'bg-bank-bg' : ''">
                            <button type="button" @click="open = open === 2 ? null : 2" class="flex w-full items-center justify-between py-3.5 text-left text-sm font-semibold text-bank-navy">
                                Apakah saya bisa berkunjung tanpa janji temu?
                                <svg class="h-4 w-4 shrink-0 text-bank-gray transition-transform" :class="open === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <p x-show="open === 2" x-cloak class="pb-4 text-xs leading-relaxed text-bank-gray">
                                Bisa. Namun kami menyarankan menghubungi pegawai yang dituju terlebih dahulu agar kunjungan Anda lebih cepat diproses oleh resepsionis.
                            </p>
                        </div>
                        <div class="rounded-xl px-4 py-1" :class="open === 3 ? 'bg-bank-bg' : ''">
                            <button type="button" @click="open = open === 3 ? null : 3" class="flex w-full items-center justify-between py-3.5 text-left text-sm font-semibold text-bank-navy">
                                Apa saja yang dilarang dibawa ke area pelabuhan?
                                <svg class="h-4 w-4 shrink-0 text-bank-gray transition-transform" :class="open === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <p x-show="open === 3" x-cloak class="pb-4 text-xs leading-relaxed text-bank-gray">
                                Sesuai ketentuan keamanan ISPS, senjata, bahan mudah terbakar, dan barang berbahaya lainnya dilarang dibawa ke area terminal. Petugas keamanan berhak melakukan pemeriksaan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

{{-- ============ CTA + FORM (form muncul setelah tombol diklik) ============ --}}
        <div>
        <section class="bg-bank-bg px-5 py-16 text-center">
            <h2 class="text-2xl font-bold text-bank-navy sm:text-3xl">Siap melakukan kunjungan?</h2>
            <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-bank-gray">
                Lengkapi formulir kedatangan sekarang — prosesnya cepat, hanya beberapa langkah di layar kiosk ini.
            </p>
            <button type="button" @click="$dispatch('open-form')" class="mt-6 inline-block rounded-xl bg-bank-blue px-8 py-3 text-sm font-semibold text-white shadow-xl shadow-bank-blue/30 transition-colors hover:bg-indigo-700">Isi Formulir Kedatangan</button>
        </section>

        {{-- ============ FORM KIOSK ============ --}}
        <div id="formulir" x-show="formOpen" x-cloak class="mx-auto max-w-3xl scroll-mt-8 px-4 pb-16 pt-4">
            <form id="kiosk-form" class="bg-white rounded-3xl shadow-sm overflow-hidden">
                @csrf
                <div class="px-6 py-6 sm:p-8 space-y-5">
                    <h2 class="text-lg font-semibold text-bank-navy">Formulir Kedatangan</h2>

                    <div>
                        <label class="block text-sm font-medium text-bank-navy mb-1">Nama Lengkap</label>
                        <input type="text" name="visitor_name" required class="w-full rounded-xl border-bank-light border px-3 py-2.5 text-sm text-bank-navy placeholder-bank-gray focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 focus:outline-none" placeholder="Nama Anda">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-bank-navy mb-1">No. Telepon</label>
                            <input type="text" name="visitor_phone" required class="w-full rounded-xl border-bank-light border px-3 py-2.5 text-sm text-bank-navy placeholder-bank-gray focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 focus:outline-none" placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-bank-navy mb-1">Instansi / Perusahaan</label>
                            <input type="text" name="visitor_institution" required class="w-full rounded-xl border-bank-light border px-3 py-2.5 text-sm text-bank-navy placeholder-bank-gray focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 focus:outline-none" placeholder="Nama instansi">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-bank-navy mb-1">Pegawai yang Dituju</label>
                        <select name="employee_id" class="w-full rounded-xl border-bank-light border px-3 py-2.5 text-sm text-bank-navy bg-white focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 focus:outline-none">
                            <option value="">— Pilih Pegawai —</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} — {{ $employee->department }}</option>
                            @endforeach
                        </select>
                    </div>
<div>
                        <label class="block text-sm font-medium text-bank-navy mb-2">Jenis Kunjungan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 rounded-xl border border-bank-light px-3 py-2.5 text-sm text-bank-navy cursor-pointer hover:bg-bank-bg/60 transition-colors">
                                <input type="radio" name="visit_type" value="meet" checked class="accent-[#1814F3]" onchange="document.getElementById('delivery-options').classList.add('hidden')">
                                Menemui Pegawai
                            </label>
                            <label class="flex items-center gap-2 rounded-xl border border-bank-light px-3 py-2.5 text-sm text-bank-navy cursor-pointer hover:bg-bank-bg/60 transition-colors">
                                <input type="radio" name="visit_type" value="deliver" class="accent-[#1814F3]" onchange="document.getElementById('delivery-options').classList.remove('hidden')">
                                Mengantar Surat / Dokumen
                            </label>
                            <label class="flex items-center gap-2 rounded-xl border border-bank-light px-3 py-2.5 text-sm text-bank-navy cursor-pointer hover:bg-bank-bg/60 transition-colors">
                                <input type="radio" name="visit_type" value="meeting_invitation" class="accent-[#1814F3]" onchange="document.getElementById('delivery-options').classList.add('hidden')">
                                Undangan Rapat / Kegiatan
                            </label>
                        </div>
                        <div id="delivery-options" class="hidden mt-2 rounded-xl bg-amber-50 border border-amber-200 p-3">
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
                        <textarea name="purpose" rows="3" required class="w-full rounded-xl border-bank-light border px-3 py-2.5 text-sm text-bank-navy placeholder-bank-gray focus:border-bank-blue focus:ring-2 focus:ring-bank-blue/20 focus:outline-none" placeholder="Tuliskan keperluan kunjungan Anda"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-bank-navy mb-2">Foto Wajah (Wajib)</label>
                        <div id="camera-box" class="rounded-2xl overflow-hidden bg-bank-bg border border-bank-light relative">
                            <video id="video" autoplay playsinline class="w-full aspect-video object-cover"></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            <div id="camera-off" class="absolute inset-0 flex items-center justify-center text-sm text-bank-gray">Menginisialisasi kamera...</div>
                            <div id="capture-overlay" class="hidden absolute inset-0 bg-black/60 flex items-center justify-center">
                                <img id="captured" class="max-h-full" alt="captured">
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="button" id="snap-btn" class="rounded-xl bg-bank-blue px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 disabled:opacity-50 transition-colors">Ambil Foto</button>
                            <button type="button" id="switch-camera-btn" class="rounded-xl bg-white px-4 py-2.5 text-sm font-medium text-bank-navy ring-1 ring-bank-light hover:bg-bank-bg transition-colors">Switch Kamera</button>
                            <button type="button" id="retake-btn" class="hidden rounded-xl bg-bank-light px-4 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/70 transition-colors">Ulangi</button>
                        </div>
                        <input type="hidden" id="photo" name="photo">
                    </div>

                    <button type="submit" id="submit-btn" class="w-full rounded-xl bg-bank-blue py-3 text-white font-semibold shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                        Kirim Kunjungan
                    </button>
                    <p id="form-error" class="hidden text-sm text-bank-red"></p>
                </div>
            </form>
<!-- Success modal -->
            <div id="success-modal" class="hidden fixed inset-0 bg-bank-navy/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="bg-white text-bank-navy rounded-3xl max-w-md w-full p-6 text-center shadow-xl">
                    <div id="blacklist-alert" class="hidden mb-4 rounded-xl bg-red-50 border border-red-200 text-bank-red px-4 py-3 text-sm">
                        ⚠️ Nama anda terdeteksi dalam daftar hitam. Mohon menunggu dan segera laporkan diri kepada resepsionis.
                    </div>
                    <div class="mx-auto h-14 w-14 rounded-full bg-bank-bg flex items-center justify-center mb-3">
                        <svg class="h-8 w-8 text-bank-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-bank-navy">Kunjungan Terkirim</h2>
                    <p id="success-msg" class="mt-2 text-sm text-bank-gray"></p>
                    <a id="badge-link" href="#" target="_blank" class="mt-5 inline-block rounded-xl bg-bank-blue px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 transition-colors">Lihat QR Badge</a>
                    <button type="button" onClick="location.reload()" class="mt-3 block w-full rounded-xl bg-bank-light px-5 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/70 transition-colors">Selesai</button>
                </div>
            </div>
        </div>
        </div><!-- /wrapper CTA + form -->

        {{-- ============ FOOTER ============ --}}
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
                        <li><a href="#formulir" @click.prevent="$dispatch('open-form')" class="transition-colors hover:text-white">Formulir Kunjungan</a></li>
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
                submitBtn.textContent = 'Mengirim...';

                const formData = new FormData(form);
                try {
                    const res = await fetch('{{ route("kiosk.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || 'Terjadi kesalahan.');
                    }
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
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Kirim Kunjungan';
                }
            });
        })();
        </script>
    </body>
</html>
