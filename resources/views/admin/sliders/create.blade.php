@extends('layouts.admin')
@section('title', 'Yeni Slayt')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.sliders._form')
    </form>
</div>
@endsection
