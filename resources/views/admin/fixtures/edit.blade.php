@extends('layouts.admin')
@section('title', 'Maç Düzenle')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('admin.fixtures.update', $fixture) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.fixtures._form')
    </form>
</div>
@endsection
