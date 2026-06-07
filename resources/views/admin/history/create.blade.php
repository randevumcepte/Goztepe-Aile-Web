@extends('layouts.admin')
@section('title', 'Şanlı Tarihimiz — Yeni Kayıt')

@section('content')
<form method="POST" action="{{ route('admin.history.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf
    @include('admin.history._form')
</form>
@endsection
