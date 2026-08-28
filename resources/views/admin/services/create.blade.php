@extends('layouts.operator')

@section('page_title', 'Tambah Layanan')

@section('content')
    @include('admin.services._form', ['service' => new \App\Models\Service()])
@endsection