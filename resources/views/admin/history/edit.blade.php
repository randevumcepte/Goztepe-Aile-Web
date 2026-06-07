@extends('layouts.admin')
@section('title', 'Şanlı Tarihimiz — Düzenle')

@section('content')
<form method="POST" action="{{ route('admin.history.update', $event) }}" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf @method('PUT')
    @include('admin.history._form')
</form>
@endsection
