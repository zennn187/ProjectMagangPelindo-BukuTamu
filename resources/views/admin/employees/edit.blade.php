@extends('layouts.operator')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-bank-navy">Ubah Pegawai</h1>
        <p class="text-sm text-bank-gray">Perbarui data pegawai {{ $employee->name }}.</p>
    </div>
    <div class="max-w-2xl rounded-3xl bg-white p-6 shadow-sm border border-bank-light/80">
        <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
            @csrf
            @method('PUT')
            @include('admin.employees._form')
        </form>
    </div>
@endsection
