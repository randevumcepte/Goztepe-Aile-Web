@extends('layouts.admin')
@section('title', 'Bestelerimiz — Düzenle')

@section('content')
<form method="POST" action="{{ route('admin.chants.update', $chant) }}" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf @method('PUT')
    @include('admin.chants._form')
</form>
@endsection
