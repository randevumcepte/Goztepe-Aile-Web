@extends('layouts.admin')
@section('title', 'Sponsor Düzenle')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('admin.sponsors.update', $sponsor) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.sponsors._form')
    </form>
</div>
@endsection
