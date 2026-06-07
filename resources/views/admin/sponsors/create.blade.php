@extends('layouts.admin')
@section('title', 'Yeni Sponsor')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('admin.sponsors.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.sponsors._form')
    </form>
</div>
@endsection
