@extends('layouts.operator')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Tambah Pengguna</h1>
        <p class="text-sm text-gray-500">Buat akun admin atau resepsionis.</p>
    </div>
    <div class="max-w-2xl rounded-xl glass-card p-6">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users._form')
        </form>
    </div>
@endsection