@extends('layouts.operator')

@section('page_title', 'Edit Layanan')

@section('content')
    @include('admin.services._form', ['service' => $service])
@endsection