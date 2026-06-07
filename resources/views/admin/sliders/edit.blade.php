@extends('layouts.admin')
@section('title', 'Slayt Düzenle')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('admin.sliders.update', $slider) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.sliders._form')
    </form>
</div>
@endsection
