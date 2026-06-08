@extends('layouts.admin')
@section('title', 'Efsaneler — Düzenle')

@section('content')
<form method="POST" action="{{ route('admin.legends.update', $legend) }}" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf @method('PUT')
    @include('admin.legends._form')
</form>
@endsection
