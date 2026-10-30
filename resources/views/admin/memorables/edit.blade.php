@extends('layouts.admin')
@section('title', 'Unutulmazlar — Düzenle')

@section('content')
<form method="POST" action="{{ route('admin.memorables.update', $memorable) }}" enctype="multipart/form-data" class="mx-auto max-w-3xl">
    @csrf @method('PUT')
    @include('admin.memorables._form')
</form>
@endsection
