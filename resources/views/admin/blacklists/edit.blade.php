@extends('layouts.operator')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-bank-navy">Ubah Daftar Hitam</h1>
        <p class="text-sm text-bank-gray">Perbarui entri daftar hitam.</p>
    </div>
    <div class="max-w-2xl rounded-3xl bg-white p-6 shadow-sm border border-bank-light/80">
        <form method="POST" action="{{ route('admin.blacklists.update', $blacklist) }}">
            @csrf
            @method('PUT')
            @include('admin.blacklists._form')
        </form>
    </div>
@endsection
