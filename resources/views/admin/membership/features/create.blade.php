@extends('layouts.admin')
@section('title', 'Yeni Avantaj')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('admin.membership.features.store') }}">
        @csrf
        @include('admin.membership.features._form')
    </form>
</div>
@endsection
