@extends('layouts.admin')
@section('title', 'Yeni Maç')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('admin.fixtures.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.fixtures._form')
    </form>
</div>
@endsection
