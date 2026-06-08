@extends('layouts.admin')
@section('title', 'Efsaneler — Yeni Kayıt')

@section('content')
<form method="POST" action="{{ route('admin.legends.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl">
    @csrf
    @include('admin.legends._form')
</form>
@endsection
