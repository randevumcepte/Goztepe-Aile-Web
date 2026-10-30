@extends('layouts.admin')
@section('title', 'Göztepe Ayrıcalık — Düzenle')

@section('content')
<form method="POST" action="{{ route('admin.partners.update', $partner) }}" enctype="multipart/form-data" class="mx-auto max-w-3xl">
    @csrf @method('PUT')
    @include('admin.partners._form')
</form>
@endsection
