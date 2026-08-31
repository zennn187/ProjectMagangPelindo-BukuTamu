<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'BukuTamu'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased text-bank-navy min-h-screen relative">
        <div class="min-h-screen" x-data="{ open: false }">
            <div class="pointer-events-none fixed inset-0 -z-10 bg-bank-bg"></div>

            <div class="xl:hidden sticky top-0 z-30 flex items-center gap-3 bg-white border-b border-bank-light text-bank-navy px-4 h-14">
                <button @click="open = true" class="p-2 -ml-2 rounded-md hover:bg-bank-light" aria-label="Buka menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
                <div class="flex items-center gap-2">
                    <span class="h-7 w-7 rounded-lg bg-bank-blue grid place-items-center font-bold text-white text-xs">BT</span>
                    <span class="font-semibold text-sm">Buku Tamu</span>
                </div>
            </div>

            <div x-show="open" @click="open = false" class="fixed inset-0 z-30 bg-black/40 xl:hidden" x-cloak></div>

            <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white flex flex-col transition-transform duration-200 -translate-x-full xl:translate-x-0" :class="open ? 'translate-x-0' : '-translate-x-full xl:translate-x-0'">
                <div class="h-16 flex items-center gap-3 px-6 shrink-0">
                    <div class="h-9 w-9 rounded-lg bg-bank-blue grid place-items-center font-bold text-white text-sm">BT</div>
                    <div>
                        <p class="text-sm font-bold leading-tight text-bank-navy">Buku Tamu</p>
                        <p class="text-[11px] text-bank-gray">Pelindo Regional 1</p>
                    </div>
                    <button @click="open = false" class="ml-auto xl:hidden -mr-1 p-1 rounded-md hover:bg-bank-light" aria-label="Tutup">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto py-4 space-y-1">
                    @php
                        $bankMenu = fn (string $label, bool $active) => 'relative flex items-center py-3 pr-3 text-sm font-medium transition-colors group ' . ($active ? 'text-bank-blue' : 'text-bank-gray hover:text-bank-navy');
                        $bankBar  = fn (bool $active) => 'absolute left-0 top-1 bottom-1 w-1 rounded-r-full ' . ($active ? 'bg-bank-blue' : 'bg-transparent group-hover:bg-bank-light');
                    @endphp

                    <a href="{{ route('dashboard') }}" class="{{ $bankMenu('Dashboard', request()->routeIs('dashboard')) }}"><span class="{{ $bankBar(request()->routeIs('dashboard')) }}"></span><span class="pl-6">Dashboard</span></a>
                    <a href="{{ route('services.index') }}" class="{{ $bankMenu('Layanan', request()->routeIs('services.index') || request()->routeIs('admin.services*')) }}"><span class="{{ $bankBar(request()->routeIs('services.index') || request()->routeIs('admin.services*')) }}"></span><span class="pl-6">Layanan</span></a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.employees.index') }}" class="{{ $bankMenu('Pegawai', request()->routeIs('admin.employees*')) }}"><span class="{{ $bankBar(request()->routeIs('admin.employees*')) }}"></span><span class="pl-6">Pegawai</span></a>
                        <a href="{{ route('admin.blacklists.index') }}" class="{{ $bankMenu('Daftar Hitam', request()->routeIs('admin.blacklists*')) }}"><span class="{{ $bankBar(request()->routeIs('admin.blacklists*')) }}"></span><span class="pl-6">Daftar Hitam</span></a>
                        <a href="{{ route('admin.users.index') }}" class="{{ $bankMenu('Pengguna', request()->routeIs('admin.users*')) }}"><span class="{{ $bankBar(request()->routeIs('admin.users*')) }}"></span><span class="pl-6">Pengguna</span></a>
                        <a href="{{ route('admin.reports.index') }}" class="{{ $bankMenu('Laporan', request()->routeIs('admin.reports*')) }}"><span class="{{ $bankBar(request()->routeIs('admin.reports*')) }}"></span><span class="pl-6">Laporan</span></a>
                    @endif

                    <a href="{{ route('profile.edit') }}" class="{{ $bankMenu('Setting', request()->routeIs('profile*')) }}"><span class="{{ $bankBar(request()->routeIs('profile*')) }}"></span><span class="pl-6">Setting</span></a>
                </nav>

                <div class="border-t border-bank-light p-4 mt-auto shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-bank-light grid place-items-center font-semibold text-bank-blue uppercase">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold truncate text-bank-navy">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-bank-gray capitalize">{{ auth()->user()->role }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-bank-gray hover:text-bank-red" title="Keluar"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg></button></form>
                    </div>
                </div>
            </aside>

            <main class="p-4 sm:p-6 2xl:p-8 transition-all xl:ml-64 min-h-screen">
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
