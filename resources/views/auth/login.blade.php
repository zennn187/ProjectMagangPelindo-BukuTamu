<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col lg:flex-row">

        <!-- ===== Kolom Kiri : Form Login ===== -->
        <div class="flex w-full lg:w-1/2 flex-col px-6 sm:px-16 xl:px-24 py-10">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 mb-10">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke halaman tamu
            </a>

            <div class="my-auto w-full max-w-md mx-auto">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">Sign In</h1>
                <p class="mt-2 text-gray-500">Masuk untuk Admin &amp; Resepsionis Buku Tamu Pelindo</p>

                <x-auth-session-status class="mt-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-8" x-data="{ showPassword: false }">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Email -->
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   required autofocus autocomplete="username"
                                   placeholder="nama@pelindo.id"
                                   class="w-full rounded-lg border border-gray-300 bg-white py-3 pl-11 pr-4 text-sm text-gray-800 placeholder-gray-400 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-5">
                        <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </span>
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                                   required autocomplete="current-password" placeholder="••••••••"
                                   class="w-full rounded-lg border border-gray-300 bg-white py-3 pl-11 pr-11 text-sm text-gray-800 placeholder-gray-400 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20" />
                            <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600"
                                    aria-label="Tampilkan password">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="mt-5 flex items-center justify-between">
                        <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2 text-sm text-gray-600">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
                            Tetap masuk
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="mt-7 w-full rounded-lg bg-brand-500 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        Sign In
                    </button>

                    </form>
            </div>

            <p class="mt-10 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} PT Pelindo &mdash; Sistem Buku Tamu
            </p>
        </div>

        <!-- ===== Kolom Kanan : Panel Brand ===== -->
        <div class="relative hidden w-1/2 overflow-hidden bg-brand-900 lg:flex lg:items-center lg:justify-center">
            <div class="absolute inset-0 opacity-40"
                 style="background-image: linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px); background-size: 64px 64px;"></div>
            <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-brand-500/10 blur-3xl"></div>

            <div class="relative z-10 max-w-md px-8 text-center">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-500 shadow-lg">
                    <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-white">Buku Tamu Pelindo</h2>
                <p class="mt-3 text-brand-200">
                    Sistem manajemen kunjungan tamu &mdash; check-in, check-out, laporan, dan manajemen pegawai dalam satu tempat.
                </p>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-guest-layout>