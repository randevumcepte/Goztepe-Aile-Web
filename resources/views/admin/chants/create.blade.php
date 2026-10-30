@extends('layouts.admin')
@section('title', 'Bestelerimiz — Yeni Kayıt')

@section('content')
<form method="POST" action="{{ route('admin.chants.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf
    @include('admin.chants._form')
</form>
@endsection
