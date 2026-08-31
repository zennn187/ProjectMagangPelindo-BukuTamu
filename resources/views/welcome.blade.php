<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Buku Tamu') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-bank-bg text-bank-navy antialiased selection:bg-bank-blue/20">
        <header class="sticky top-0 z-40 border-b border-white/10 bg-bank-navy/85 backdrop-blur-xl">
            <nav class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3.5 md:px-8">
                <a href="#top" class="flex items-center gap-3 min-w-0">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2a7 7 0 0 0-7 7v10.5a.5.5 0 0 0 .8.4L12 15l6.2 4.9a.5.5 0 0 0 .8-.4V9a7 7 0 0 0-7-7zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/>
                        </svg>
                    </span>
                    <span class="leading-tight text-white">
                        <span class="block text-[10px] font-bold tracking-[0.24em] sm:text-xs">PELINDO</span>
                        <span class="block text-[9px] text-white/70 sm:text-[10px]">Regional 1 Dumai</span>
                    </span>
                </a>

                <div class="hidden items-center gap-8 text-sm text-white/70 md:flex">
                    <a href="#fitur" class="transition-colors hover:text-white">Fitur</a>
                    <a href="#layanan" class="transition-colors hover:text-white">Layanan</a>
                    <a href="#tentang" class="transition-colors hover:text-white">Tentang</a>
                    <a href="#faq" class="transition-colors hover:text-white">FAQ</a>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-2 sm:gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-xl bg-white px-3 py-2 text-xs font-semibold text-bank-navy transition-colors hover:bg-bank-light sm:px-4 sm:text-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-xl border border-white/15 bg-white/5 px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-white/10 sm:px-4 sm:text-sm">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-xl bg-bank-blue px-3 py-2 text-xs font-semibold text-white shadow-lg shadow-bank-blue/30 transition-colors hover:bg-indigo-700 sm:px-4 sm:text-sm">Daftar</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </nav>
        </header>

        <main id="top">
            <section class="relative overflow-hidden bg-bank-navy text-white">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(94,136,255,0.22),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(76,201,240,0.18),_transparent_28%)]"></div>
                <div class="absolute -left-20 top-20 h-72 w-72 rounded-full bg-bank-blue/30 blur-3xl"></div>
                <div class="absolute right-0 top-10 h-80 w-80 rounded-full bg-indigo-400/20 blur-3xl"></div>

                <div class="relative mx-auto grid max-w-6xl gap-10 px-4 pb-14 pt-10 sm:gap-12 sm:px-5 sm:pt-14 md:px-8 lg:grid-cols-[1.08fr_0.92fr] lg:items-center lg:gap-12 lg:pb-20 lg:pt-20">
                    <div class="max-w-xl">
                        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-[10px] font-medium uppercase tracking-[0.18em] text-white/80 sm:text-[11px]">
                            <span class="h-2.5 w-2.5 rounded-full bg-bank-green"></span>
                            Buku Tamu Digital Pelindo
                        </div>

                        <h1 class="text-3xl font-extrabold leading-[1.05] tracking-[-0.05em] text-white sm:text-4xl lg:text-5xl xl:text-6xl">
                            Kunjungan Anda, tercatat cepat dan aman.
                        </h1>

                        <p class="mt-4 max-w-lg text-sm leading-relaxed text-white/70 sm:mt-5 sm:text-base">
                            Sistem kunjungan digital untuk area pelabuhan, kantor, dan fasilitas operasional Pelindo Dumai.
                            Proses check-in lebih cepat, data lebih rapi, dan verifikasi menjadi lebih terukur.
                        </p>

                        <div class="mt-7 flex flex-col gap-3 sm:mt-8 sm:flex-row sm:flex-wrap">
                            <a href="{{ route('login') }}" class="w-full rounded-xl bg-bank-blue px-5 py-3 text-center text-sm font-semibold text-white shadow-[0_18px_40px_rgba(24,20,243,0.38)] transition-colors hover:bg-indigo-700 sm:w-auto">Masuk ke Dashboard</a>
                            <a href="#layanan" class="w-full rounded-xl border border-white/20 bg-white/5 px-5 py-3 text-center text-sm font-semibold text-white transition-colors hover:bg-white/10 sm:w-auto">Lihat Layanan</a>
                        </div>

                        <div class="mt-8 grid max-w-lg gap-3 sm:grid-cols-3 sm:gap-5">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
                                <p class="text-xl font-bold text-white sm:text-2xl">24/7</p>
                                <p class="mt-1 text-[10px] uppercase tracking-[0.12em] text-white/55 sm:text-[11px]">Operasional</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
                                <p class="text-xl font-bold text-white sm:text-2xl">2 Menit</p>
                                <p class="mt-1 text-[10px] uppercase tracking-[0.12em] text-white/55 sm:text-[11px]">Check-in</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
                                <p class="text-xl font-bold text-white sm:text-2xl">100%</p>
                                <p class="mt-1 text-[10px] uppercase tracking-[0.12em] text-white/55 sm:text-[11px]">Digital</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative mx-auto w-full max-w-lg lg:mx-0 lg:max-w-none">
                        <div class="relative overflow-hidden rounded-[28px] border border-white/10 bg-white/5 p-3 shadow-[0_30px_80px_rgba(15,23,42,0.35)] backdrop-blur-sm sm:p-4">
                            <div class="rounded-[22px] bg-white p-4 text-bank-navy shadow-2xl sm:p-5">
                                <div class="flex items-start justify-between gap-4 border-b border-bank-light pb-4">
                                    <div>
                                        <p class="text-[11px] uppercase tracking-[0.16em] text-bank-gray">Status hari ini</p>
                                        <h2 class="mt-2 text-2xl font-bold text-bank-navy">Kunjungan</h2>
                                    </div>
                                    <span class="rounded-full bg-bank-green/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-bank-green">Aktif</span>
                                </div>

                                <div class="mt-5 space-y-4">
                                    <div class="rounded-2xl bg-bank-bg p-4">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm text-bank-gray">Check-in masuk</p>
                                            <span class="text-lg font-bold text-bank-navy">48</span>
                                        </div>
                                        <div class="mt-2 h-2 rounded-full bg-white">
                                            <div class="h-2 w-[72%] rounded-full bg-bank-blue"></div>
                                        </div>
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div class="rounded-2xl border border-bank-light bg-white p-4">
                                            <p class="text-[11px] uppercase tracking-[0.14em] text-bank-gray">Tertunda</p>
                                            <p class="mt-3 text-2xl font-bold text-bank-navy">14</p>
                                        </div>
                                        <div class="rounded-2xl border border-bank-light bg-white p-4">
                                            <p class="text-[11px] uppercase tracking-[0.14em] text-bank-gray">Selesai</p>
                                            <p class="mt-3 text-2xl font-bold text-bank-navy">31</p>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl bg-bank-navy p-4 text-white">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm text-white/70">Kebutuhan hari ini</p>
                                            <span class="text-sm font-semibold text-white">87%</span>
                                        </div>
                                        <div class="mt-3 h-2 rounded-full bg-white/10">
                                            <div class="h-2 w-[87%] rounded-full bg-bank-blue"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="fitur" class="bg-white py-14 sm:py-16 lg:py-20">
                <div class="mx-auto max-w-6xl px-4 sm:px-5 md:px-8">
                    <div class="max-w-2xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-bank-blue">Fitur utama</p>
                        <h2 class="mt-3 text-2xl font-bold tracking-[-0.04em] text-bank-navy sm:text-3xl lg:text-4xl">Desain yang fokus pada kecepatan dan kontrol.</h2>
                    </div>

                    <div class="mt-8 grid gap-5 md:mt-10 md:grid-cols-3 md:gap-6">
                        <article class="rounded-[24px] border border-bank-light bg-bank-bg p-5 shadow-sm sm:p-6">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-bank-blue/10 text-bank-blue">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9Z"/><path d="M8 8h8M8 12h8M8 16h5" stroke-linecap="round"/></svg>
                            </div>
                            <h3 class="mt-5 text-xl font-semibold text-bank-navy">Kiosk digital</h3>
                            <p class="mt-3 text-sm leading-relaxed text-bank-gray">
                                Formulir kunjungan dapat diisi secara mandiri dengan foto identitas, tujuan, dan opsi jenis kunjungan yang jelas.
                            </p>
                        </article>

                        <article class="rounded-[24px] border border-bank-light bg-bank-bg p-6 shadow-sm">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-bank-green/10 text-bank-green">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12.5 9.2 16.7 19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <h3 class="mt-5 text-xl font-semibold text-bank-navy">Verifikasi cepat</h3>
                            <p class="mt-3 text-sm leading-relaxed text-bank-gray">
                                Resepsionis dapat menerima, menunda, menolak, atau menyelesaikan kunjungan hanya dalam satu alur yang konsisten.
                            </p>
                        </article>

                        <article class="rounded-[24px] border border-bank-light bg-bank-bg p-6 shadow-sm">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-bank-yellow/15 text-bank-yellow">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 18V7.5A2.5 2.5 0 0 1 7.5 5h9A2.5 2.5 0 0 1 19 7.5V18M8 9h8M8 13h8M8 17h5" stroke-linecap="round"/></svg>
                            </div>
                            <h3 class="mt-5 text-xl font-semibold text-bank-navy">Laporan real-time</h3>
                            <p class="mt-3 text-sm leading-relaxed text-bank-gray">
                                Statistik kunjungan, status, dan data rekam jejak dapat dipantau secara cepat untuk kebutuhan operasional.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="layanan" class="bg-bank-bg py-14 sm:py-16 lg:py-20">
                <div class="mx-auto max-w-6xl px-4 sm:px-5 md:px-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-bank-blue">Layanan</p>
                            <h2 class="mt-3 text-2xl font-bold tracking-[-0.04em] text-bank-navy sm:text-3xl lg:text-4xl">Semua kebutuhan tamu dalam satu titik akses.</h2>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-5 md:mt-10 lg:grid-cols-3">
                        <article class="overflow-hidden rounded-[24px] border border-bank-light bg-white shadow-sm">
                            <div class="h-44 bg-[linear-gradient(135deg,#dfeafe,#c5d5ff)] p-5">
                                <div class="flex h-full items-end rounded-[20px] border border-white/40 bg-white/25 p-4 backdrop-blur-sm">
                                    <div>
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-bank-navy/70">Keperluan</p>
                                        <h3 class="mt-2 text-lg font-semibold text-bank-navy">Kunjungan kerja</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5">
                                <p class="text-sm leading-relaxed text-bank-gray">
                                    Untuk pegawai, vendor, mitra, dan tamu yang melaksanakan aktivitas operasional di area pelabuhan.
                                </p>
                            </div>
                        </article>

                        <article class="overflow-hidden rounded-[24px] border border-bank-light bg-white shadow-sm">
                            <div class="h-44 bg-[linear-gradient(135deg,#d8f5ef,#c6efe3)] p-5">
                                <div class="flex h-full items-end rounded-[20px] border border-white/40 bg-white/25 p-4 backdrop-blur-sm">
                                    <div>
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-bank-navy/70">Penyerahan</p>
                                        <h3 class="mt-2 text-lg font-semibold text-bank-navy">Dokumen / barang</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5">
                                <p class="text-sm leading-relaxed text-bank-gray">
                                    Jenis kunjungan pengiriman surat, dokumen, atau kebutuhan administrasi yang perlu diterima secara tertib.
                                </p>
                            </div>
                        </article>

                        <article class="overflow-hidden rounded-[24px] border border-bank-light bg-white shadow-sm">
                            <div class="h-44 bg-[linear-gradient(135deg,#fff2d0,#f9e7be)] p-5">
                                <div class="flex h-full items-end rounded-[20px] border border-white/40 bg-white/25 p-4 backdrop-blur-sm">
                                    <div>
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-bank-navy/70">Kegiatan</p>
                                        <h3 class="mt-2 text-lg font-semibold text-bank-navy">Undangan rapat</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5">
                                <p class="text-sm leading-relaxed text-bank-gray">
                                    Proses kedatangan untuk acara, rapat, koordinasi, dan kegiatan operasional resmi di lingkungan Pelindo.
                                </p>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="tentang" class="bg-white py-14 sm:py-16 lg:py-20">
                <div class="mx-auto max-w-6xl px-4 sm:px-5 md:px-8">
                    <div class="grid items-center gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:gap-10">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-bank-blue">Tentang kami</p>
                            <h2 class="mt-4 text-2xl font-bold tracking-[-0.04em] text-bank-navy sm:text-3xl lg:text-4xl">Sistem yang menjaga keamanan dan kenyamanan kunjungan.</h2>
                            <p class="mt-5 max-w-xl text-base leading-relaxed text-bank-gray">
                                Buku tamu digital Pelindo dirancang untuk mengelola kedatangan tamu secara efisien, akuntabel, dan sesuai kebutuhan operasional pelabuhan. Setiap kunjungan memiliki jejak yang jelas mulai dari check-in hingga status selesai.
                            </p>
                        </div>

                        <div class="rounded-[28px] border border-bank-light bg-bank-bg p-6 shadow-sm">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl border border-bank-light bg-white p-4">
                                    <p class="text-[11px] uppercase tracking-[0.16em] text-bank-gray">Tamu</p>
                                    <p class="mt-3 text-3xl font-bold text-bank-navy">1.2K</p>
                                    <p class="mt-2 text-sm text-bank-gray">Kunjungan terdaftar</p>
                                </div>
                                <div class="rounded-2xl border border-bank-light bg-white p-4">
                                    <p class="text-[11px] uppercase tracking-[0.16em] text-bank-gray">Verifikasi</p>
                                    <p class="mt-3 text-3xl font-bold text-bank-blue">96%</p>
                                    <p class="mt-2 text-sm text-bank-gray">Tingkat kelengkapan</p>
                                </div>
                                <div class="rounded-2xl border border-bank-light bg-white p-4 sm:col-span-2">
                                    <p class="text-[11px] uppercase tracking-[0.16em] text-bank-gray">Proses</p>
                                    <div class="mt-4 space-y-4">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-bank-blue text-sm font-semibold text-white">1</span>
                                            <p class="text-sm text-bank-gray">Isi formulir kunjungan dan lampirkan foto identitas.</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-bank-green text-sm font-semibold text-white">2</span>
                                            <p class="text-sm text-bank-gray">Resepsionis memvalidasi dan menetapkan status tamu.</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-bank-yellow text-sm font-semibold text-white">3</span>
                                            <p class="text-sm text-bank-gray">Badge QR dan rekam jejak siap dipantau di dashboard.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="faq" class="bg-bank-navy py-14 text-white sm:py-16 lg:py-20">
                <div class="mx-auto max-w-6xl px-4 sm:px-5 md:px-8">
                    <div class="max-w-2xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">FAQ</p>
                        <h2 class="mt-3 text-2xl font-bold tracking-[-0.04em] text-white sm:text-3xl lg:text-4xl">Pertanyaan paling sering ditanyakan.</h2>
                    </div>

                    <div class="mt-8 grid gap-4 sm:mt-10 lg:grid-cols-2">
                        <details class="group rounded-2xl border border-white/10 bg-white/5 p-5 open:bg-white/8" open>
                            <summary class="cursor-pointer list-none text-base font-semibold text-white">Apakah tamu perlu membawa dokumen khusus?</summary>
                            <p class="mt-3 text-sm leading-relaxed text-white/70">Umumnya cukup membawa identitas diri dan menyebutkan tujuan kunjungan. Formulir akan menampung data yang dibutuhkan untuk verifikasi.</p>
                        </details>

                        <details class="group rounded-2xl border border-white/10 bg-white/5 p-5 open:bg-white/8">
                            <summary class="cursor-pointer list-none text-base font-semibold text-white">Bagaimana proses persetujuan tamu?</summary>
                            <p class="mt-3 text-sm leading-relaxed text-white/70">Setelah form terisi, resepsionis akan menilai status kunjungan dan memberitahu apakah tamu dapat diterima, ditunda, atau ditolak.</p>
                        </details>

                        <details class="group rounded-2xl border border-white/10 bg-white/5 p-5 open:bg-white/8">
                            <summary class="cursor-pointer list-none text-base font-semibold text-white">Apakah sistem ini cocok untuk kegiatan rapat?</summary>
                            <p class="mt-3 text-sm leading-relaxed text-white/70">Ya. Jenis kunjungan “Undangan Rapat / Kegiatan” sudah tersedia agar proses kedatangan untuk acara resmi lebih terstruktur.</p>
                        </details>

                        <details class="group rounded-2xl border border-white/10 bg-white/5 p-5 open:bg-white/8">
                            <summary class="cursor-pointer list-none text-base font-semibold text-white">Bisakah data kunjungan dilihat real-time?</summary>
                            <p class="mt-3 text-sm leading-relaxed text-white/70">Ya. Dashboard menampilkan status terkini, jumlah kunjungan, serta catatan operasional yang dapat diakses oleh petugas yang berwenang.</p>
                        </details>
                    </div>
                </div>
            </section>

            <section class="bg-white py-14 sm:py-16 lg:py-20">
                <div class="mx-auto max-w-5xl px-4 text-center sm:px-5 md:px-8">
                    <h2 class="text-2xl font-bold tracking-[-0.04em] text-bank-navy sm:text-3xl lg:text-4xl">Siap mengelola kunjungan dengan cara yang lebih tertata?</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-bank-gray sm:text-base">
                        Sistem ini dibuat untuk membantu pelabuhan, kantor, dan unit operasional menjaga keamanan, efisiensi, serta kesejajaran data tamu secara konsisten.
                    </p>
                    <div class="mt-8 flex flex-col items-stretch justify-center gap-3 sm:flex-row">
                        <a href="{{ route('login') }}" class="rounded-xl bg-bank-blue px-6 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_rgba(24,20,243,0.38)] transition-colors hover:bg-indigo-700">Masuk ke Sistem</a>
                        <a href="#top" class="rounded-xl border border-bank-light bg-bank-bg px-6 py-3 text-sm font-semibold text-bank-navy transition-colors hover:bg-bank-light">Kembali ke Atas</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-bank-light bg-white">
            <div class="mx-auto grid max-w-6xl gap-10 px-5 py-10 md:grid-cols-4 md:px-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-bank-blue text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2a7 7 0 0 0-7 7v10.5a.5.5 0 0 0 .8.4L12 15l6.2 4.9a.5.5 0 0 0 .8-.4V9a7 7 0 0 0-7-7zm0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/>
                            </svg>
                        </span>
                        <span class="leading-tight text-bank-navy">
                            <span class="block text-xs font-bold tracking-[0.22em]">PELINDO</span>
                            <span class="block text-[10px] text-bank-gray">Regional 1 Dumai</span>
                        </span>
                    </div>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-bank-gray">
                        Pelabuhan Indonesia Regional 1 Dumai menghadirkan sistem kunjungan digital yang modern, cepat, dan terstruktur untuk mendukung operasional sehari-hari.
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.15em] text-bank-navy">Navigasi</h3>
                    <ul class="mt-4 space-y-2 text-sm text-bank-gray">
                        <li><a href="#fitur" class="transition-colors hover:text-bank-navy">Fitur</a></li>
                        <li><a href="#layanan" class="transition-colors hover:text-bank-navy">Layanan</a></li>
                        <li><a href="#tentang" class="transition-colors hover:text-bank-navy">Tentang</a></li>
                        <li><a href="#faq" class="transition-colors hover:text-bank-navy">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.15em] text-bank-navy">Kontak</h3>
                    <ul class="mt-4 space-y-2 text-sm text-bank-gray">
                        <li>Pelabuhan Dumai</li>
                        <li>Riau, Indonesia</li>
                        <li><a href="https://pelindo.id" target="_blank" rel="noopener" class="transition-colors hover:text-bank-navy">pelindo.id</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-bank-light bg-bank-bg py-4 text-center text-[11px] uppercase tracking-[0.12em] text-bank-gray">
                © {{ date('Y') }} PT Pelabuhan Indonesia (Persero) — Regional 1 Dumai
            </div>
        </footer>
    </body>
</html>
