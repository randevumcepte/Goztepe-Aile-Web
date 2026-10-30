@extends('layouts.admin')
@section('title', 'Unutulmazlar — Yeni Kayıt')

@section('content')
<form method="POST" action="{{ route('admin.memorables.store') }}" enctype="multipart/form-data" class="mx-auto max-w-3xl">
    @csrf
    @include('admin.memorables._form')
</form>
@endsection
