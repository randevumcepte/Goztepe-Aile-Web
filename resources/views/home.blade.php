@extends('layouts.public')
@section('title', 'Göztepe Tribünleri — İzmir\'in Gür Sesi')

@php
    $tl = fn ($v) => number_format((float) $v, 0, ',', '.') . ' ₺';
@endphp

@section('content')

{{-- HERO --}}
@if ($sliders->isNotEmpty())
    {{-- Yönetilebilir slider --}}
    <section class="relative min-h-[64vh]" x-data="{ i:0, n:{{ $sliders->count() }} }" x-init="if(n>1) setInterval(()=>{ i=(i+1)%n }, 6000)">
        @foreach ($sliders as $idx => $sl)
            <div x-show="i==={{ $idx }}" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0">
                @if ($sl->imageUrl())
                    <img src="{{ $sl->imageUrl() }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-700 via-brand-800 to-ink"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/70 to-ink/20"></div>
                <div class="relative mx-auto flex min-h-[64vh] max-w-7xl flex-col justify-end px-4 pb-16 pt-24">
                    <h1 class="max-w-3xl text-4xl font-bold uppercase leading-tight text-white sm:text-6xl">{{ $sl->title }}</h1>
                    @if ($sl->subtitle)<p class="mt-4 max-w-2xl text-lg text-white/80">{{ $sl->subtitle }}</p>@endif
                    @if ($sl->cta_label && $sl->cta_url)
                        <div class="mt-6"><a href="{{ $sl->cta_url }}" class="rounded-lg bg-gold px-6 py-3 text-sm font-bold uppercase text-brand-800 hover:bg-gold-400">{{ $sl->cta_label }}</a></div>
                    @endif
                </div>
            </div>
        @endforeach
        @if ($sliders->count() > 1)
            <div class="absolute bottom-5 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                @foreach ($sliders as $idx => $sl)
                    <button @click="i={{ $idx }}" :class="i==={{ $idx }} ? 'bg-gold w-6' : 'bg-white/50 w-2.5'" class="h-2.5 rounded-full transition-all"></button>
                @endforeach
            </div>
        @endif
    </section>
@else
    {{-- Slider yoksa: öne çıkan haber veya varsayılan --}}
    <section class="relative">
        @php $cover = $featured?->coverUrl(); @endphp
        <div class="relative min-h-[62vh] overflow-hidden">
            @if ($cover)
                <img src="{{ $cover }}" alt="" class="absolute inset-0 h-full w-full object-cover">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-brand-700 via-brand-800 to-ink"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/70 to-ink/20"></div>
            <div class="relative mx-auto flex min-h-[62vh] max-w-7xl flex-col justify-end px-4 pb-12 pt-24">
                @if ($featured)
                    <span class="mb-3 inline-flex w-fit items-center rounded-full bg-gold px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-800">{{ $featured->categoryLabel() }}</span>
                    <h1 class="max-w-3xl text-4xl font-bold uppercase leading-tight text-white sm:text-5xl">{{ $featured->title }}</h1>
                    @if ($featured->excerpt)
                        <p class="mt-4 max-w-2xl text-lg text-white/80">{{ \Illuminate\Support\Str::limit($featured->excerpt, 160) }}</p>
                    @endif
                    <div class="mt-6 flex items-center gap-4">
                        <a href="{{ route('haberler.show', $featured) }}" class="rounded-lg bg-gold px-6 py-3 text-sm font-bold uppercase text-brand-800 hover:bg-gold-400">Devamını Oku</a>
                        <span class="text-sm text-white/60">{{ $featured->published_at?->locale('tr')->translatedFormat('d F Y') }}</span>
                    </div>
                @else
                    <h1 class="max-w-3xl text-5xl font-bold uppercase leading-tight text-white sm:text-6xl">İzmir'in<br><span class="text-gold">Gür Sesi</span></h1>
                    <p class="mt-4 max-w-2xl text-lg text-white/80">Taraftarın gücüyle; şeffaf, dayanışmacı ve gururlu bir camia. Sen de tribünün bir parçası ol.</p>
                    <div class="mt-6 flex gap-4">
                        <a href="{{ route('register') }}" class="rounded-lg bg-gold px-6 py-3 text-sm font-bold uppercase text-brand-800 hover:bg-gold-400">Üye Ol</a>
                        <a href="{{ route('seffaf-kasa') }}" class="rounded-lg border border-white/30 px-6 py-3 text-sm font-bold uppercase text-white hover:bg-white/10">Şeffaf Kasa</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- İKİNCİL HABERLER --}}
@if ($secondary->isNotEmpty())
<section class="mx-auto max-w-7xl px-4 py-12">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @foreach ($secondary as $p)
            <a href="{{ route('haberler.show', $p) }}" class="group relative overflow-hidden rounded-2xl bg-brand-800">
                <div class="aspect-[16/9] overflow-hidden">
                    @if ($p->coverUrl())
                        <img src="{{ $p->coverUrl() }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        @include('partials.cover-fallback')
                    @endif
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-ink/90 to-transparent"></div>
                <div class="absolute bottom-0 p-5">
                    <span class="mb-2 inline-block rounded bg-gold px-2 py-0.5 text-[11px] font-bold uppercase text-brand-800">{{ $p->categoryLabel() }}</span>
                    <h3 class="text-xl font-bold uppercase leading-snug text-white group-hover:text-gold">{{ $p->title }}</h3>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ŞEFFAF KASA ŞERİDİ --}}
<section class="border-y border-white/10 bg-gradient-to-r from-brand-800 to-brand-900">
    <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-8 px-4 py-12 lg:grid-cols-2">
        <div>
            <span class="text-sm font-bold uppercase tracking-widest text-gold">Şeffaflık</span>
            <h2 class="mt-2 text-3xl font-bold uppercase text-white">Her Kuruş Açık</h2>
            <p class="mt-3 max-w-md text-white/70">Aidat, bağış ve tüm harcamalar — faturalara kadar açık. Taraftarın parası, taraftarın gözü önünde.</p>
            <a href="{{ route('seffaf-kasa') }}" class="mt-5 inline-block rounded-lg bg-gold px-6 py-3 text-sm font-bold uppercase text-brand-800 hover:bg-gold-400">Kasayı İncele</a>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-2xl bg-white/5 p-5 text-center ring-1 ring-white/10">
                <p class="text-xs font-semibold uppercase text-emerald-400">Gelir</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ $tl($totals['gelir']) }}</p>
            </div>
            <div class="rounded-2xl bg-white/5 p-5 text-center ring-1 ring-white/10">
                <p class="text-xs font-semibold uppercase text-rose-400">Gider</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ $tl($totals['gider']) }}</p>
            </div>
            <div class="rounded-2xl bg-gold p-5 text-center">
                <p class="text-xs font-semibold uppercase text-brand-800">Bakiye</p>
                <p class="mt-2 text-2xl font-bold text-brand-900">{{ $tl($totals['bakiye']) }}</p>
            </div>
        </div>
    </div>
</section>

{{-- SON HABERLER --}}
@if ($rest->isNotEmpty())
<section class="mx-auto max-w-7xl px-4 py-14">
    <div class="mb-6 flex items-end justify-between">
        <h2 class="text-2xl font-bold uppercase text-white">Son Haberler</h2>
        <a href="{{ route('haberler.index') }}" class="text-sm font-semibold uppercase text-gold hover:underline">Tümü →</a>
    </div>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($rest as $p)
            <a href="{{ route('haberler.show', $p) }}" class="group overflow-hidden rounded-2xl bg-brand-800 ring-1 ring-white/10 transition hover:ring-gold/50">
                <div class="aspect-[16/10] overflow-hidden">
                    @if ($p->coverUrl())
                        <img src="{{ $p->coverUrl() }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        @include('partials.cover-fallback')
                    @endif
                </div>
                <div class="p-4">
                    <span class="text-[11px] font-bold uppercase text-gold">{{ $p->categoryLabel() }}</span>
                    <h3 class="mt-1 line-clamp-2 font-semibold leading-snug text-white group-hover:text-gold">{{ $p->title }}</h3>
                    <p class="mt-2 text-xs text-white/50">{{ $p->published_at?->locale('tr')->translatedFormat('d F Y') }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ÜYELİK CTA --}}
<section class="mx-auto max-w-7xl px-4 py-14">
    <div class="rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 p-8 sm:p-12">
        <div class="text-center">
            <h2 class="text-3xl font-bold uppercase text-white">Sen de Tribünün Parçası Ol</h2>
            <p class="mx-auto mt-3 max-w-2xl text-white/80">Üye ol; aidatın ve bağışın tribünün gücüne dönüşsün. Kategoriler:</p>
        </div>
        <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach (['Öğrenci'=>'Genç taraftar', 'Standart'=>'Ana üyelik', 'Destekçi'=>'Daha çok destek', 'Asıl Üye'=>'Oy hakkı'] as $cat=>$desc)
                <div class="rounded-2xl bg-white/10 p-5 text-center ring-1 ring-white/15">
                    <p class="font-display text-lg font-bold uppercase text-gold">{{ $cat }}</p>
                    <p class="mt-1 text-sm text-white/70">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
        <div class="mt-8 text-center">
            <a href="{{ route('register') }}" class="inline-block rounded-lg bg-gold px-8 py-3 text-sm font-bold uppercase text-brand-800 hover:bg-gold-400">Hemen Üye Ol</a>
        </div>
    </div>
</section>

{{-- SPONSORLAR (placeholder) --}}
<section class="border-t border-white/10 bg-brand-900">
    <div class="mx-auto max-w-7xl px-4 py-12 text-center">
        <span class="text-sm font-bold uppercase tracking-widest text-gold">Birlikte Güçlüyüz</span>
        <h2 class="mt-2 text-2xl font-bold uppercase text-white">Sponsorlarımız</h2>
        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @forelse ($sponsors as $sp)
                <a @if($sp->url) href="{{ $sp->url }}" target="_blank" @endif class="grid h-20 place-items-center rounded-xl bg-white/95 px-4 ring-1 ring-white/10 transition hover:scale-105">
                    @if ($sp->logoUrl())
                        <img src="{{ $sp->logoUrl() }}" alt="{{ $sp->name }}" class="max-h-12 max-w-full object-contain">
                    @else
                        <span class="text-sm font-bold text-brand-700">{{ $sp->name }}</span>
                    @endif
                </a>
            @empty
                @for ($i=0; $i<5; $i++)
                    <div class="grid h-20 place-items-center rounded-xl bg-white/5 text-sm font-semibold text-white/30 ring-1 ring-white/10">Logo</div>
                @endfor
            @endforelse
        </div>
        <a href="{{ route('iletisim') }}" class="mt-8 inline-block rounded-lg border border-gold/50 px-6 py-2.5 text-sm font-bold uppercase text-gold hover:bg-gold/10">Sponsor Ol</a>
    </div>
</section>

@endsection
