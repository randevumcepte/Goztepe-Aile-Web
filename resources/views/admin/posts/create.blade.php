@extends('layouts.admin')
@section('title', 'Yeni Haber')

@section('content')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.posts._form')
    </form>
</div>
@endsection
