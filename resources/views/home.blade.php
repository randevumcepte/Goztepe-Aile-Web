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

{{-- HAFTANIN MAÇI --}}
@if ($nextMatch)
<section class="relative overflow-hidden border-b border-white/10 bg-gradient-to-br from-brand-700 via-brand-800 to-ink">
    <div class="pointer-events-none absolute inset-0 opacity-[0.06]" style="background-image:repeating-linear-gradient(45deg,#F7B500 0 3px,transparent 3px 18px)"></div>

    <div class="relative mx-auto max-w-5xl px-4 py-12">
        <div class="text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-gold px-4 py-1 text-xs font-extrabold uppercase tracking-widest text-brand-800">
                <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-600 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-brand-700"></span></span>
                Haftanın Maçı
            </span>
            <p class="mt-2 text-sm font-semibold uppercase tracking-wide text-white/70">{{ $nextMatch->competition }} · {{ $nextMatch->homeAwayLabel() }}</p>
        </div>

        {{-- Takımlar --}}
        <div class="mt-8 grid grid-cols-3 items-center gap-2 sm:gap-6">
            {{-- Göztepe --}}
            <div class="flex flex-col items-center gap-3 text-center">
                <img src="{{ asset('img/logo.png') }}" alt="Göztepe" class="h-20 w-20 rounded-xl object-contain sm:h-24 sm:w-24">
                <span class="font-display text-base font-bold uppercase text-white sm:text-xl">Göztepe</span>
            </div>

            {{-- Orta: skor veya VS --}}
            <div class="flex flex-col items-center">
                @if ($nextMatch->isPlayed())
                    <span class="font-display text-4xl font-bold text-gold sm:text-5xl">{{ $nextMatch->home_score }} - {{ $nextMatch->away_score }}</span>
                @else
                    <span class="font-display text-3xl font-bold text-white/30 sm:text-4xl">VS</span>
                @endif
                <span class="mt-2 rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase text-white/80">
                    {{ $nextMatch->kickoff_at->locale('tr')->translatedFormat('H:i') }}
                </span>
            </div>

            {{-- Rakip --}}
            <div class="flex flex-col items-center gap-3 text-center">
                @if ($nextMatch->opponentLogoUrl())
                    <img src="{{ $nextMatch->opponentLogoUrl() }}" alt="{{ $nextMatch->opponent }}" class="h-20 w-20 rounded-xl bg-white/95 object-contain p-1 sm:h-24 sm:w-24">
                @else
                    <span class="grid h-20 w-20 place-items-center rounded-xl bg-white/10 font-display text-3xl font-bold text-white sm:h-24 sm:w-24">{{ mb_substr($nextMatch->opponent, 0, 1) }}</span>
                @endif
                <span class="font-display text-base font-bold uppercase text-white sm:text-xl">{{ $nextMatch->opponent }}</span>
            </div>
        </div>

        {{-- Detaylar --}}
        <div class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-white/80">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                {{ $nextMatch->kickoff_at->locale('tr')->translatedFormat('d F Y, l') }}
            </span>
            @if ($nextMatch->venue)
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                    {{ $nextMatch->venue }}
                </span>
            @endif
            @if ($nextMatch->broadcast)
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" /></svg>
                    {{ $nextMatch->broadcast }}
                </span>
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

{{-- HOŞ GELDİN / NEDEN BURADAYIZ --}}
<section class="relative overflow-hidden bg-ink">
    {{-- Arka plan resmi (yeni görsel gelince bu satırdaki dosya adını değiştir) --}}
    <div class="pointer-events-none absolute inset-0 bg-cover bg-center"
         style="background-image:url('{{ asset('uploads/hakkimizda.jpg') }}')"></div>
    {{-- Yumuşak karartma: üst ve alt kenarlar tam siyaha eriyor (geçiş belli olmaz), ortada görsel hafif görünür --}}
    <div class="pointer-events-none absolute inset-0"
         style="background:linear-gradient(to bottom, #0b0b12 0%, rgba(11,11,18,0.78) 28%, rgba(11,11,18,0.78) 72%, #0b0b12 100%)"></div>

    <div class="relative mx-auto max-w-4xl px-4 py-20 sm:py-24">
        <div class="text-center">
            <span class="inline-block rounded-full border border-gold/40 bg-gold/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-gold">İzmir'in Gür Sesi · 1925</span>
            <h2 class="mt-5 font-display text-4xl font-bold uppercase leading-tight text-white sm:text-5xl">
                Hoş Geldin, <span class="text-gold">Sarı-Kırmızılı</span>
            </h2>
        </div>

        <div class="mx-auto mt-8 max-w-3xl space-y-5 text-lg leading-relaxed text-white/80">
            <p>
                Burası bir kulübün değil, bir <strong class="text-white">sevdanın</strong> evi. Göztepe;
                1925'te bir avuç insanın yüreğinde başlayan, bugün milyonların kalbinde atan bir çınar.
                Bu çınarı bir asırdır ayakta tutan ise her hafta sahanın kenarında haykıran, yağmurda ıslanan,
                deplasmanda yolları aşan <strong class="text-white">tribün</strong> oldu.
            </p>
            <p>
                Tribün olmak kolay değil. Bir koreografinin arkasında haftalarca süren emek, geceler boyu kesilen
                kartonlar, cepten çıkan paralar var. Bir deplasman için yüzlerce kilometre yol, bilet, otobüs,
                bazen kapanan kapılar var. Çoğu zaman sesimizi duyuran tek güç kendi imkânlarımız oldu; ne bir
                sırt sıvazlayan, ne de yükü paylaşan çıktı. Her şeyi taraftar kendi cebinden, kendi gönlünden karşıladı.
            </p>
            <p>
                Yıllarca dağınık kaldık. Kimi zaman bir koreografi yarım kaldı, kimi zaman toplanan paranın
                nereye gittiği soru işareti oldu. Güç vardı ama dağınıktı; gönül vardı ama düzeni yoktu.
                İşte bu sistemi tam da bunun için kurduk.
            </p>

            <blockquote class="border-l-4 border-gold pl-5 py-1">
                <p class="font-display text-xl font-bold uppercase leading-snug text-gold sm:text-2xl">
                    "Tek tek güçsüzüz; ama bir araya gelince İzmir'in gür sesiyiz."
                </p>
            </blockquote>

            <p>
                Bu çatı altında her kuruş açık, her harcama faturasına kadar görünür. Aidatın ve bağışın doğrudan
                tribünün gücüne dönüşür; koreografiye, deplasmana, ihtiyacı olan kardeşimize. Burada kimse
                tek başına değil, kimse karanlıkta kalmıyor. Çünkü tribünün parası, tribünün gözü önünde olmalı.
            </p>
            <p>
                Sen de bu sesin bir parçasısın. Gel, dağınık gücümüzü tek yürek yapalım; bu tarihi birlikte yazmaya devam edelim.
            </p>
        </div>

        <div class="mt-10 flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}" class="rounded-lg bg-gold px-7 py-3 text-sm font-bold uppercase tracking-wide text-brand-800 transition hover:bg-gold-400">Aramıza Katıl</a>
            <a href="{{ route('sanli-tarihimiz') }}" class="rounded-lg border border-white/20 px-7 py-3 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-white/10">Şanlı Tarihimiz</a>
        </div>
    </div>
</section>

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
@if ($plans->isNotEmpty())
<section id="uyelik" class="relative overflow-hidden border-y-2 border-gold/40 bg-gradient-to-br from-brand-700 via-brand-800 to-ink py-16">
    <div class="absolute inset-0 opacity-[0.06]" style="background-image:repeating-linear-gradient(45deg,#F7B500 0 3px,transparent 3px 18px)"></div>
    <div class="relative mx-auto max-w-7xl px-4">
        <div class="text-center">
            <span class="inline-block rounded-full bg-gold px-4 py-1 text-xs font-extrabold uppercase tracking-widest text-brand-800">Üyelik</span>
            <h2 class="mt-4 text-4xl font-bold uppercase text-white sm:text-5xl">Sen de Tribünün Parçası Ol</h2>
            <p class="mx-auto mt-3 max-w-2xl text-lg text-white/80">Aidatın ve bağışın doğrudan tribünün gücüne dönüşür. Sana uygun üyeliği seç:</p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($plans as $p)
                <div class="relative flex flex-col rounded-2xl bg-gradient-to-br from-[#FFD24A] to-gold p-6 text-brand-900 shadow-lg transition
                            {{ $p->is_popular ? 'ring-4 ring-white shadow-2xl lg:-translate-y-3' : 'ring-1 ring-amber-300 hover:-translate-y-1' }}">
                    @if ($p->is_popular)
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-brand-600 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wide text-white shadow">★ En Popüler</span>
                    @endif
                    <p class="font-display text-xl font-bold uppercase text-brand-700">{{ $p->name }}</p>
                    <p class="mt-1 text-sm font-medium text-brand-900/70">{{ $p->description }}</p>
                    <div class="mt-4 flex items-end gap-1">
                        <span class="text-4xl font-extrabold text-brand-900">{{ $p->price }}₺</span>
                        <span class="mb-1 text-sm font-medium text-brand-900/70">/ yıl</span>
                    </div>
                    <ul class="mt-5 flex-1 space-y-2 text-sm">
                        @foreach ($p->card_features ?? [] as $oz)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-brand-700" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <span class="font-medium text-brand-900/80">{{ $oz }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}"
                       class="mt-6 block rounded-lg bg-brand-600 px-4 py-2.5 text-center text-sm font-bold uppercase text-white transition hover:bg-brand-700">
                        Üye Ol
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('uyelik.avantajlar') }}" class="inline-flex items-center gap-2 rounded-lg border border-gold/60 px-6 py-2.5 text-sm font-bold uppercase text-gold hover:bg-gold/10">Tüm Avantajları Karşılaştır →</a>
        </div>
        <p class="mt-6 text-center text-sm text-white/60">Tüm üyelikler yıllıktır · Ödemen şeffaf kasada görünür · İstediğin zaman yükseltebilirsin</p>
    </div>
</section>
@endif

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
