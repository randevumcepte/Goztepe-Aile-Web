@extends('layouts.public')
@section('title', 'Haberler — Göztepe Tribünleri')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-12">
    <div class="mb-8 border-l-4 border-gold pl-4">
        <h1 class="text-3xl font-bold uppercase text-white">Haberler</h1>
        <p class="mt-1 text-white/60">Tribünden son gelişmeler, duyurular ve maç değerlendirmeleri</p>
    </div>

    @if ($posts->isEmpty())
        <div class="rounded-2xl bg-brand-800 p-12 text-center text-white/50 ring-1 ring-white/10">Henüz haber yayınlanmadı.</div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $p)
                <a href="{{ route('haberler.show', $p) }}" class="group overflow-hidden rounded-2xl bg-brand-800 ring-1 ring-white/10 transition hover:ring-gold/50">
                    <div class="aspect-[16/10] overflow-hidden">
                        @if ($p->coverUrl())
                            <img src="{{ $p->coverUrl() }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="grid h-full w-full place-items-center bg-gradient-to-br from-brand-600 to-brand-900 font-display text-2xl font-bold uppercase text-white/30">GÖZTEPE</div>
                        @endif
                    </div>
                    <div class="p-5">
                        <span class="text-[11px] font-bold uppercase text-gold">{{ $p->categoryLabel() }}</span>
                        <h3 class="mt-1 line-clamp-2 text-lg font-semibold leading-snug text-white group-hover:text-gold">{{ $p->title }}</h3>
                        @if ($p->excerpt)<p class="mt-2 line-clamp-2 text-sm text-white/60">{{ $p->excerpt }}</p>@endif
                        <p class="mt-3 text-xs text-white/40">{{ $p->published_at?->locale('tr')->translatedFormat('d F Y') }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
