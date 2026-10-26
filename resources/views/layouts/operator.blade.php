<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'BukuTamu'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <script>
            try {
                const savedTheme = localStorage.getItem('bukutamu-theme');
                if (savedTheme === 'dark' || savedTheme === 'light') {
                    document.documentElement.dataset.theme = savedTheme;
                }
            } catch (error) {
                console.warn('Preferensi tema tidak dapat dibaca.', error);
            }
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="operator-shell font-sans antialiased text-bank-navy min-h-screen relative" x-data="{
        open: false,
        darkMode: document.documentElement.dataset.theme === 'dark',
        toggleTheme() {
            this.darkMode = !this.darkMode;
            const theme = this.darkMode ? 'dark' : 'light';
            document.documentElement.dataset.theme = theme;
            try {
                localStorage.setItem('bukutamu-theme', theme);
            } catch (error) {
                console.warn('Preferensi tema tidak dapat disimpan.', error);
            }
        }
    }">
        <div class="min-h-screen">
            <div class="pointer-events-none fixed inset-0 -z-10 bg-bank-bg"></div>

            <div class="operator-mobile-header xl:hidden sticky top-0 z-30 flex items-center gap-3 bg-white border-b border-bank-light text-bank-navy px-4 h-14">
                <button @click="open = true" class="p-2 -ml-2 rounded-md hover:bg-bank-light" aria-label="Buka menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
                <div class="flex min-w-0 items-center gap-2">
                    <img src="{{ asset('images/logo_pelindo.png') }}" alt="Logo Pelindo" class="h-7 w-20 object-contain">
                    <span class="font-semibold text-sm">Buku Tamu</span>
                </div>
                <button type="button" @click="toggleTheme()" :aria-label="darkMode ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'" :title="darkMode ? 'Mode terang' : 'Mode gelap'" class="ml-auto flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-bank-bg text-bank-navy ring-1 ring-bank-light transition-colors hover:bg-bank-light">
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                    <svg x-show="darkMode" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.2 15.3A8.5 8.5 0 0 1 8.7 3.8 8.5 8.5 0 1 0 20.2 15.3Z"/></svg>
                </button>
            </div>

            <div x-show="open" @click="open = false" class="fixed inset-0 z-30 bg-black/40 xl:hidden" x-cloak></div>

            <aside class="operator-sidebar fixed inset-y-0 left-0 z-40 w-64 bg-white flex flex-col transition-transform duration-200 -translate-x-full xl:translate-x-0" :class="open ? 'translate-x-0' : '-translate-x-full xl:translate-x-0'">
                <div class="relative flex min-h-[104px] shrink-0 items-center gap-3 border-b border-bank-light/70 px-5 py-5">
                    <div class="operator-brand-tile flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white ring-1 ring-bank-light">
                        <img src="{{ asset('images/logo_pelindo_aja.png') }}" alt="Logo Pelindo" class="h-full w-full object-contain p-1">
                    </div>
                    <div class="min-w-0 pr-5">
                        <p class="text-[15px] font-bold leading-tight text-bank-navy">Buku Tamu</p>
                        <p class="mt-1 text-[11px] leading-4 tracking-wide text-bank-gray">Pelindo Regional 1 Dumai</p>
                    </div>
                    <button @click="open = false" class="absolute right-3 top-3 rounded-md p-1.5 hover:bg-bank-light xl:hidden" aria-label="Tutup">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto py-4 space-y-1">
                    @php
                        $bankMenu = fn (string $label, bool $active) => 'relative flex items-center py-3 pr-3 text-sm font-medium transition-colors group ' . ($active ? 'text-bank-blue' : 'text-bank-gray hover:text-bank-navy');
                        $bankBar  = fn (bool $active) => 'absolute left-0 top-1 bottom-1 w-1 rounded-r-full ' . ($active ? 'bg-bank-blue' : 'bg-transparent group-hover:bg-bank-light');
                    @endphp

                    <a href="{{ route('dashboard') }}" class="{{ $bankMenu('Dashboard', request()->routeIs('dashboard')) }}"><span class="{{ $bankBar(request()->routeIs('dashboard')) }}"></span><span class="pl-6">Dashboard</span></a>
                    <a href="{{ route('gate.scanner') }}" class="{{ $bankMenu('Scanner Gate', request()->routeIs('gate.scanner') || request()->routeIs('gate.scan')) }}"><span class="{{ $bankBar(request()->routeIs('gate.scanner') || request()->routeIs('gate.scan')) }}"></span><span class="pl-6">Scanner Gate</span></a>
                    <a href="{{ route('gate-reports.index') }}" class="{{ $bankMenu('Kedatangan Tamu', request()->routeIs('gate-reports*')) }}"><span class="{{ $bankBar(request()->routeIs('gate-reports*')) }}"></span><span class="pl-6">Kedatangan Tamu</span></a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.employees.index') }}" class="{{ $bankMenu('Pegawai', request()->routeIs('admin.employees*')) }}"><span class="{{ $bankBar(request()->routeIs('admin.employees*')) }}"></span><span class="pl-6">Pegawai</span></a>
                        <a href="{{ route('admin.blacklists.index') }}" class="{{ $bankMenu('Daftar Hitam', request()->routeIs('admin.blacklists*')) }}"><span class="{{ $bankBar(request()->routeIs('admin.blacklists*')) }}"></span><span class="pl-6">Daftar Hitam</span></a>
                        <a href="{{ route('admin.users.index') }}" class="{{ $bankMenu('Pengguna', request()->routeIs('admin.users*')) }}"><span class="{{ $bankBar(request()->routeIs('admin.users*')) }}"></span><span class="pl-6">Pengguna</span></a>
                        <a href="{{ route('admin.reports.index') }}" class="{{ $bankMenu('Laporan', request()->routeIs('admin.reports*')) }}"><span class="{{ $bankBar(request()->routeIs('admin.reports*')) }}"></span><span class="pl-6">Laporan</span></a>
                    @endif

                    <a href="{{ route('profile.edit') }}" class="{{ $bankMenu('Setting', request()->routeIs('profile*')) }}"><span class="{{ $bankBar(request()->routeIs('profile*')) }}"></span><span class="pl-6">Setting</span></a>
                </nav>

                <div class="mt-auto shrink-0 border-t border-bank-light p-4">
                    <button type="button" @click="toggleTheme()" class="mb-4 flex w-full items-center gap-3 rounded-xl bg-bank-bg px-3 py-2.5 text-left text-sm font-medium text-bank-navy transition-colors hover:bg-bank-light">
                        <span class="operator-brand-tile flex h-8 w-8 items-center justify-center rounded-lg bg-white ring-1 ring-bank-light" :class="darkMode ? 'text-bank-blue' : 'text-bank-navy'">
                            <svg x-show="!darkMode" class="operator-theme-sun h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="#091a3a" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                            <svg x-show="darkMode" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.2 15.3A8.5 8.5 0 0 1 8.7 3.8 8.5 8.5 0 1 0 20.2 15.3Z"/></svg>
                        </span>
                        <span x-text="darkMode ? 'Mode terang' : 'Mode gelap'"></span>
                    </button>
                    <div class="flex items-center gap-3">
                        <div class="operator-brand-tile flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white ring-1 ring-bank-light">
                            <img src="{{ asset('images/logo_pelindo_aja.png') }}" alt="" class="h-full w-full object-contain p-1">
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold truncate text-bank-navy">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-bank-gray capitalize">{{ auth()->user()->role }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-bank-gray hover:text-bank-red" title="Keluar"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg></button></form>
                    </div>
                </div>
            </aside>

            <main class="operator-main p-4 sm:p-6 2xl:p-8 transition-all xl:ml-64 min-h-screen">
                @hasSection('page_title')
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                        <h1 class="text-2xl sm:text-[26px] font-medium text-bank-navy">@yield('page_title')</h1>
                        @stack('header_actions')
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
        <div id="toast-container" class="toast-container"></div>
        @stack('scripts')
    </body>
</html>
