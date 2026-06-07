@extends('layouts.admin')
@section('title', 'Haber Düzenle')

@section('content')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.posts._form')
    </form>
</div>
@endsection
