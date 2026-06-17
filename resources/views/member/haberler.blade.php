@extends('layouts.member')
@section('title', 'Haberler')

@section('content')
<div class="max-w-5xl">
    @if ($posts->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($posts as $post)
                <a href="{{ route('haberler.show', $post) }}"
                   class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white hover:shadow-md transition">
                    <div class="aspect-[16/9] overflow-hidden bg-slate-100">
                        @if ($post->coverUrl())
                            <img src="{{ $post->coverUrl() }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="grid h-full w-full place-items-center text-slate-300">
                                <img src="{{ asset('img/logo.png') }}" alt="" class="h-12 w-12 opacity-40">
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col p-4">
                        <span class="mb-2 inline-block w-fit rounded-full bg-brand-50 px-2.5 py-0.5 text-[11px] font-bold uppercase text-brand-700">{{ $post->categoryLabel() }}</span>
                        <h3 class="font-bold leading-snug group-hover:text-brand-600">{{ $post->title }}</h3>
                        @if ($post->excerpt)
                            <p class="mt-1 text-sm text-slate-500 line-clamp-2">{{ \Illuminate\Support\Str::limit($post->excerpt, 100) }}</p>
                        @endif
                        <p class="mt-auto pt-3 text-xs text-slate-400">{{ $post->published_at?->format('d.m.Y') }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $posts->links() }}</div>
    @else
        <div class="rounded-xl border border-slate-200 bg-white px-5 py-12 text-center text-slate-400">Henüz haber yok.</div>
    @endif
</div>
@endsection
