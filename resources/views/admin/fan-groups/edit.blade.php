@extends('layouts.admin')
@section('title', 'Taraftar Grupları — Düzenle')

@section('content')
<form method="POST" action="{{ route('admin.fan-groups.update', $group) }}" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf @method('PUT')
    @include('admin.fan-groups._form')
</form>
@endsection
