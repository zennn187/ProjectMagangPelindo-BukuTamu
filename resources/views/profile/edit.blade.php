@extends('layouts.operator')

@section('page_title', 'Setting')

@section('content')
    @php $user = auth()->user(); @endphp

    <div class="max-w-4xl">
        {{-- Kartu profil (avatar + role) --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm mb-6">
            <div class="flex flex-col sm:flex-row items-center gap-5">
                <div class="relative">
                    <div class="h-24 w-24 rounded-full bg-bank-light grid place-items-center text-3xl font-bold text-bank-blue uppercase">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <span class="absolute bottom-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-bank-blue text-white shadow">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                    </span>
                </div>
                <div class="text-center sm:text-left">
                    <h2 class="text-xl font-bold text-bank-navy">{{ $user->name }}</h2>
                    <p class="text-sm text-bank-gray">{{ $user->email }}</p>
                    <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-medium capitalize {{ $user->role === 'admin' ? 'bg-bank-blue/10 text-bank-blue' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $user->role === 'admin' ? 'Administrator' : 'Resepsionis' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Edit profile --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm mb-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Security: ganti password --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm mb-6">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Hapus akun --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection