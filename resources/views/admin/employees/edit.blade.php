@extends('layouts.operator')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Ubah Pegawai</h1>
        <p class="text-sm text-gray-500">Perbarui data pegawai {{ $employee->name }}.</p>
    </div>
    <div class="max-w-2xl rounded-xl glass-card p-6">
        <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
            @csrf
            @method('PUT')
            @include('admin.employees._form')
        </form>
    </div>
@endsection