@extends('layouts.public')
@section('title', $post->title.' — Göztepe Tribünleri')
@section('meta', \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? $post->body), 150))

@section('content')
<article class="mx-auto max-w-3xl px-4 py-12">
    <a href="{{ route('haberler.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm text-white/60 hover:text-gold">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        Haberler
    </a>

    <span class="inline-block rounded bg-gold px-2.5 py-0.5 text-[11px] font-bold uppercase text-brand-800">{{ $post->categoryLabel() }}</span>
    <h1 class="mt-3 text-3xl font-bold uppercase leading-tight text-white sm:text-4xl">{{ $post->title }}</h1>
    <p class="mt-3 text-sm text-white/50">{{ $post->published_at?->locale('tr')->translatedFormat('d F Y') }} @if($post->author) · {{ $post->author->name }} @endif</p>

    @if ($post->coverUrl())
        <img src="{{ $post->coverUrl() }}" class="mt-6 w-full rounded-2xl object-cover">
    @endif

    @if ($post->excerpt)
        <p class="mt-6 text-lg font-medium text-white/80">{{ $post->excerpt }}</p>
    @endif

    <div class="prose prose-invert mt-6 max-w-none text-white/80 leading-relaxed">
        {!! nl2br(e($post->body)) !!}
    </div>
</article>

@if ($related->isNotEmpty())
<section class="mx-auto max-w-7xl px-4 pb-16">
    <h2 class="mb-5 text-xl font-bold uppercase text-white">Benzer Haberler</h2>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        @foreach ($related as $p)
            <a href="{{ route('haberler.show', $p) }}" class="group overflow-hidden rounded-2xl bg-brand-800 ring-1 ring-white/10 hover:ring-gold/50">
                <div class="aspect-[16/10] overflow-hidden">
                    @if ($p->coverUrl())
                        <img src="{{ $p->coverUrl() }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        @include('partials.cover-fallback')
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="line-clamp-2 font-semibold text-white group-hover:text-gold">{{ $p->title }}</h3>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection
