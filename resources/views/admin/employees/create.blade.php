@extends('layouts.operator')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-bank-navy">Tambah Pegawai</h1>
        <p class="text-sm text-bank-gray">Isi data pegawai baru.</p>
    </div>
    <div class="max-w-2xl rounded-3xl bg-white p-6 sm:p-8 shadow-sm border border-bank-light/80 transition-all duration-300 hover:shadow-md">
        <form method="POST" action="{{ route('admin.employees.store') }}" data-loading-text="Menyimpan Pegawai...">
            @csrf
            @include('admin.employees._form')
        </form>
    </div>
@endsection
