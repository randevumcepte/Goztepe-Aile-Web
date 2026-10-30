@extends('layouts.admin')
@section('title', 'Göztepe Ayrıcalık — Yeni İşletme')

@section('content')
<form method="POST" action="{{ route('admin.partners.store') }}" enctype="multipart/form-data" class="mx-auto max-w-3xl">
    @csrf
    @include('admin.partners._form')
</form>
@endsection
