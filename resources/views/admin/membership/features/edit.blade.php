@extends('layouts.admin')
@section('title', 'Avantaj Düzenle')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('admin.membership.features.update', $feature) }}">
        @csrf @method('PUT')
        @include('admin.membership.features._form')
    </form>
</div>
@endsection
