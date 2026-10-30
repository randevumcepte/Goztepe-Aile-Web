@extends('layouts.admin')
@section('title', 'Taraftar Grupları — Yeni Grup')

@section('content')
<form method="POST" action="{{ route('admin.fan-groups.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf
    @include('admin.fan-groups._form')
</form>
@endsection
