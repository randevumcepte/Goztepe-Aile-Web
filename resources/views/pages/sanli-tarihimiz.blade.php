@extends('layouts.public')
@section('title', 'Şanlı Tarihimiz — Göztepe Tribünleri')
@section('meta', '1925\'ten bugüne Göztepe Spor Kulübü\'nün şanlı tarihi: Türkiye Kupaları, Avrupa\'da yarı final ve efsane kadrolar.')

@php
    // Zaman tüneli ve galeri içeriği admin panelinden yönetilir (Web Yönetimi → Şanlı Tarihimiz).
    $timeline = $timeline ?? collect();
    $gallery = $gallery ?? collect();

    // Efsaneler admin panelinden yönetilir (Web Yönetimi → Efsaneler).
    $legends = $legends ?? collect();

    // --- Onur listesi ---
    $honours = [
        ['n' => '2', 'l' => 'Türkiye Kupası', 's' => '1968-69 · 1969-70'],
        ['n' => '1', 'l' => 'Cumhurbaşkanlığı Kupası', 's' => '1970'],
        ['n' => '1', 'l' => 'Türkiye Şampiyonluğu', 's' => '1950'],
        ['n' => 'YF', 'l' => 'Avrupa Yarı Finali', 's' => 'Fuar Şehirleri Kupası 1968-69'],
    ];
@endphp

@section('content')

{{-- ============ HERO ============ --}}
<section class="relative overflow-hidden bg-ink">
    {{-- Giriş görseli --}}
    <div class="pointer-events-none absolute inset-0 bg-cover bg-center"
         style="background-image:url('{{ asset('img/sanli-tarihimiz-hero.png') }}')"></div>
    {{-- Karartma katmanları (yazılar okunsun) --}}
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-ink/90 via-ink/75 to-black/90"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.07]"
         style="background-image:radial-gradient(circle at 20% 20%, #F7B500 0, transparent 40%), radial-gradient(circle at 80% 60%, #D5102E 0, transparent 45%)"></div>
    {{-- Dev yıl filigranı --}}
    <div class="pointer-events-none absolute -right-6 -top-10 select-none font-display text-[28vw] font-bold leading-none text-white/[0.03] sm:text-[20vw]">1925</div>

    <div class="relative mx-auto max-w-7xl px-4 py-24 sm:py-32">
        <span class="inline-block rounded-full border border-gold/40 bg-gold/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-gold">İzmir'in Gür Sesi · 1925</span>
        <h1 class="mt-6 font-display text-5xl font-bold uppercase leading-[0.95] text-white sm:text-7xl lg:text-8xl">
            Şanlı <span class="text-gold">Tarihimiz</span>
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-relaxed text-white/80 sm:text-xl">
            Bir asra yaklaşan çınar... Anadolu'dan Avrupa'ya, Türkiye Kupalarından yarı finallere uzanan bir destan.
            Sarı-kırmızının her perdesi, tribünlerin haykırışıyla yazıldı.
        </p>
        <div class="mt-10 flex flex-wrap gap-3">
            <a href="#kurulus" class="rounded-lg bg-gold px-7 py-3 text-sm font-bold uppercase tracking-wide text-brand-800 transition hover:bg-gold-400">Kuruluş Hikayesi</a>
            <a href="#zaman-tuneli" class="rounded-lg border border-white/20 px-7 py-3 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-white/10">Zaman Tüneli</a>
            <a href="#galeri" class="rounded-lg border border-white/20 px-7 py-3 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-white/10">Eski Fotoğraflar</a>
        </div>
    </div>

    {{-- İstatistik şeridi --}}
    <div class="relative border-t border-white/10 bg-black/30 backdrop-blur">
        <div class="mx-auto grid max-w-7xl grid-cols-2 divide-x divide-white/10 lg:grid-cols-4">
            @foreach ([['1925','Kuruluş Yılı'],['2','Türkiye Kupası'],['İLK','Avrupa\'da Yarı Final'],['99+','Yıllık Çınar']] as [$big,$small])
                <div class="px-4 py-8 text-center">
                    <p class="font-display text-4xl font-bold text-gold sm:text-5xl">{{ $big }}</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-white/60 sm:text-sm">{{ $small }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ KURULUŞ HİKAYESİ ============ --}}
<section id="kurulus" class="relative overflow-hidden bg-gradient-to-b from-brand-900/40 to-ink py-20">
    <div class="pointer-events-none absolute -left-10 top-10 select-none font-display text-[20vw] font-bold leading-none text-white/[0.03] sm:text-[12vw]">1925</div>

    <div class="relative mx-auto max-w-6xl px-4">
        <div class="mb-14 text-center">
            <span class="text-sm font-bold uppercase tracking-widest text-gold">Bir Trende Doğan Sevda</span>
            <h2 class="mt-2 font-display text-4xl font-bold uppercase text-white sm:text-5xl">Kuruluş Hikayesi</h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-white/70">Göztepe; bir öfkeyle başlayan, bir kararla filizlenen ve bir zaferle taçlanan hikâyenin adıdır. İşte sarı-kırmızının ilk perdesi…</p>
        </div>

        {{-- Üç perde --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Perde 1: Tren --}}
            <article class="group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-brand-800/50 to-ink p-7 ring-1 ring-white/5 transition hover:border-gold/40">
                <div class="flex items-center justify-between">
                    <span class="font-display text-6xl font-bold text-white/10">I</span>
                    <span class="rounded-full bg-gold/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-gold">1925 · Aydın Dönüşü</span>
                </div>
                <h3 class="mt-4 font-display text-2xl font-bold uppercase text-gold">Tren Vagonunda Bir Karar</h3>
                <p class="mt-3 text-sm leading-relaxed text-white/75">
                    Altay, Aydın'da bir spor müsabakasındadır. Yönetimdeki <strong class="text-white">Ferit Bey</strong>'e söz hakkı verilmez.
                    İzmir'e dönüş yolunda, tekerleklerin ritmiyle çınlayan vagonlarda tartışma büyür de büyür.
                    O raylar boyunca bir fikir olgunlaşır: <em class="text-gold">Artık kendi kulüpleri olacaktır.</em>
                </p>
            </article>

            {{-- Perde 2: Mez Gazinosu --}}
            <article class="group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-brand-700/50 to-ink p-7 ring-1 ring-white/5 transition hover:border-gold/40">
                <div class="flex items-center justify-between">
                    <span class="font-display text-6xl font-bold text-white/10">II</span>
                    <span class="rounded-full bg-gold/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-gold">14 Haziran 1925</span>
                </div>
                <h3 class="mt-4 font-display text-2xl font-bold uppercase text-gold">Mez Gazinosu'nda Doğuş</h3>
                <p class="mt-3 text-sm leading-relaxed text-white/75">
                    Vapur iskelesinin hemen yanındaki <strong class="text-white">Mez Gazinosu</strong>'nda, semtin kıdemli futbolcuları ve gençleri toplanır.
                    <strong class="text-white">Nebil</strong> ve <strong class="text-white">Vedat</strong> kardeşler, <strong class="text-white">Muzaffer Koral</strong>,
                    <strong class="text-white">Ferit Simsaroğlu</strong>, <strong class="text-white">Necati</strong> ve <strong class="text-white">Nusret</strong> Bey öncülüğünde
                    Göztepe Spor Kulübü resmen kurulur. İlk başkan <strong class="text-white">Fehmi Simsaroğlu</strong>; fahri başkanlığa dönemin İzmir Valisi <strong class="text-white">Kazım Dirik</strong> seçilir.
                </p>
            </article>

            {{-- Perde 3: İlk Zafer --}}
            <article class="group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-brand-800/50 to-ink p-7 ring-1 ring-white/5 transition hover:border-gold/40">
                <div class="flex items-center justify-between">
                    <span class="font-display text-6xl font-bold text-white/10">III</span>
                    <span class="rounded-full bg-gold/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-gold">28 Ağustos 1925</span>
                </div>
                <h3 class="mt-4 font-display text-2xl font-bold uppercase text-gold">İlk Maç, İlk Zafer</h3>
                <p class="mt-3 text-sm leading-relaxed text-white/75">
                    Kuruluştan yalnızca iki ay sonra ilk resmî sınav: karşıda yine <strong class="text-white">Altay</strong>.
                    Genç Göztepe sahaya korkusuzca çıkar ve <strong class="text-gold">1-0</strong>'lık galibiyetle ayrılır.
                    Bu zafer, daha doğarken kazanmayı bilen bir camianın özgüvenini perçinler. Çınar, ilk meyvesini vermiştir.
                </p>
            </article>
        </div>

        {{-- Renklerin hikayesi --}}
        <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-3xl border border-gold/20 bg-gradient-to-br from-brand-700/40 to-ink p-8">
                <h3 class="font-display text-2xl font-bold uppercase text-white">Sarı ile Kırmızının Anlamı</h3>
                <p class="mt-3 leading-relaxed text-white/75">
                    Renkleri <strong class="text-white">Nebil (Çobanoğlu)</strong> önerir; ilhamını İzmir'in üstünde her gün doğan o kavurucu güneşten alır.
                    <span class="text-gold font-semibold">Sarı</span> isyanı, <span class="text-brand-500 font-semibold">kırmızı</span> ise geleceği ve sevdayı simgeler.
                    O günden bugüne bu iki renk, Alsancak'ın tribünlerinde dalgalanan bir bayrak, bir kimlik, bir aidiyet oldu.
                </p>
                <blockquote class="mt-6 border-l-4 border-gold pl-4">
                    <p class="font-display text-xl font-bold uppercase text-gold">"Sarı isyanı, kırmızı sevdayı simgeliyordu."</p>
                </blockquote>
            </div>

            {{-- Künye --}}
            <div class="rounded-3xl border border-white/10 bg-ink/60 p-8">
                <h3 class="font-display text-lg font-bold uppercase text-gold">Kuruluş Künyesi</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    @foreach ([
                        ['Kuruluş', '14 Haziran 1925'],
                        ['Yer', 'Mez Gazinosu, Göztepe'],
                        ['Şehir', 'İzmir'],
                        ['İlk Başkan', 'Fehmi Simsaroğlu'],
                        ['Fahri Başkan', 'Vali Kazım Dirik'],
                        ['İlk Maç', '28 Ağustos 1925 · Altay 0-1'],
                        ['Renkler', 'Sarı - Kırmızı'],
                    ] as [$k, $v])
                        <div class="flex items-center justify-between gap-3 border-b border-white/5 pb-2">
                            <dt class="text-white/55">{{ $k }}</dt>
                            <dd class="text-right font-semibold text-white">{{ $v }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        {{-- Kurucular şeridi --}}
        <div class="mt-10 rounded-3xl border border-white/10 bg-gradient-to-r from-brand-800/40 via-ink to-brand-800/40 p-8 text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-gold">Kurucu İrade</p>
            <div class="mt-4 flex flex-wrap justify-center gap-2.5">
                @foreach (['Nebil Çobanoğlu','Vedat Çobanoğlu','Muzaffer Koral','Ferit Simsaroğlu','Necati Bey','Nusret Bey','Fehmi Simsaroğlu','Kazım Dirik'] as $name)
                    <span class="rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-sm font-semibold text-white">{{ $name }}</span>
                @endforeach
            </div>
            <p class="mx-auto mt-5 max-w-2xl text-sm text-white/60">Bir avuç sevdalının trende başlattığı bu yolculuk, bugün milyonların kalbinde atan bir camiaya dönüştü.</p>
        </div>
    </div>
</section>

{{-- ============ ZAMAN TÜNELİ ============ --}}
@if ($timeline->isNotEmpty())
<section id="zaman-tuneli" class="relative bg-ink py-20">
    <div class="mx-auto max-w-5xl px-4">
        <div class="mb-14 text-center">
            <span class="text-sm font-bold uppercase tracking-widest text-gold">Kilometre Taşları</span>
            <h2 class="mt-2 font-display text-4xl font-bold uppercase text-white sm:text-5xl">Zaman Tüneli</h2>
        </div>

        <div class="relative">
            {{-- Orta dikey çizgi --}}
            <div class="absolute left-4 top-0 h-full w-px bg-gradient-to-b from-gold via-brand-600 to-transparent sm:left-1/2"></div>

            <div class="space-y-10">
                @foreach ($timeline as $i => $m)
                    <div class="relative flex items-start gap-6 sm:gap-0 {{ $i % 2 === 0 ? 'sm:flex-row' : 'sm:flex-row-reverse' }}">
                        {{-- Nokta --}}
                        <div class="absolute left-4 top-2 z-10 -translate-x-1/2 sm:left-1/2">
                            <span class="flex h-4 w-4 items-center justify-center rounded-full bg-gold ring-4 ring-gold/20"></span>
                        </div>
                        {{-- Boş yarı (masaüstü hizalama) --}}
                        <div class="hidden sm:block sm:w-1/2"></div>
                        {{-- Kart --}}
                        <div class="ml-10 w-full sm:ml-0 sm:w-1/2 {{ $i % 2 === 0 ? 'sm:pr-10 sm:text-right' : 'sm:pl-10' }}">
                            <div class="group rounded-2xl border border-white/10 bg-gradient-to-br from-brand-800/60 to-ink p-6 ring-1 ring-white/5 transition hover:border-gold/40 hover:shadow-xl hover:shadow-brand-900/40">
                                @if ($m->tag)
                                <div class="flex items-center gap-2 {{ $i % 2 === 0 ? 'sm:justify-end' : '' }}">
                                    <span class="rounded-md bg-gold/15 px-2 py-0.5 text-xs font-bold uppercase tracking-wide text-gold">{{ $m->tag }}</span>
                                </div>
                                @endif
                                <p class="mt-2 font-display text-3xl font-bold text-white">{{ $m->year }}</p>
                                <h3 class="mt-1 font-display text-xl font-bold uppercase text-gold">{{ $m->title }}</h3>
                                <p class="mt-3 text-sm leading-relaxed text-white/75">{{ $m->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============ FOTOĞRAF GALERİSİ (SHOW) ============ --}}
@if ($gallery->isNotEmpty())
@php
    $galleryItems = $gallery->map(fn($g) => [
        'src' => $g->imageUrl(),
        'cap' => $g->caption ?: $g->title,
        'year' => $g->year,
    ])->values();
@endphp
<section id="galeri" class="relative bg-gradient-to-b from-ink to-brand-900/40 py-20"
         x-data="{ open:false, i:0, items: {{ \Illuminate\Support\Js::from($galleryItems) }},
                   show(n){ this.i=n; this.open=true }, next(){ this.i=(this.i+1)%this.items.length }, prev(){ this.i=(this.i-1+this.items.length)%this.items.length } }"
         @keydown.window.escape="open=false" @keydown.window.arrow-right="if(open)next()" @keydown.window.arrow-left="if(open)prev()">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-12 text-center">
            <span class="text-sm font-bold uppercase tracking-widest text-gold">Hafızalarda Kalanlar</span>
            <h2 class="mt-2 font-display text-4xl font-bold uppercase text-white sm:text-5xl">Eski Fotoğraflar</h2>
            <p class="mx-auto mt-3 max-w-xl text-sm text-white/60">Sarı-kırmızı tarihin solmayan kareleri. Büyütmek için bir fotoğrafa dokun.</p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-3">
            @foreach ($gallery as $idx => $g)
                @php $cap = $g->caption ?: $g->title; @endphp
                <button type="button" @click="show({{ $idx }})"
                        class="group relative aspect-[4/3] overflow-hidden rounded-2xl ring-1 ring-white/10 focus:outline-none focus:ring-2 focus:ring-gold">
                    {{-- Zarif kapak (resim yoksa görünür) --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-700 via-brand-900 to-ink"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="font-display text-5xl font-bold text-white/10">{{ $g->year }}</span>
                    </div>
                    {{-- Gerçek fotoğraf (varsa kapağın üstünü örter) --}}
                    @if ($g->imageUrl())
                        <img src="{{ $g->imageUrl() }}" alt="{{ $cap }}" loading="lazy"
                             onerror="this.style.display='none'"
                             class="relative h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @endif
                    {{-- Karartma + başlık --}}
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent p-3 text-left">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-gold">{{ $g->year }}</p>
                        <p class="text-sm font-semibold text-white">{{ $cap }}</p>
                    </div>
                    {{-- Büyüteç ikonu --}}
                    <span class="absolute right-3 top-3 grid h-9 w-9 place-items-center rounded-full bg-black/40 text-white opacity-0 backdrop-blur transition group-hover:opacity-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </span>
                </button>
            @endforeach
        </div>

    </div>

    {{-- Lightbox --}}
    <div x-show="open" x-cloak @click="open=false"
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 p-4 backdrop-blur"
         x-transition.opacity>
        <button @click="open=false" class="absolute right-4 top-4 grid h-11 w-11 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <button @click.stop="prev()" class="absolute left-3 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        </button>
        <button @click.stop="next()" class="absolute right-3 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
        </button>

        <figure @click.stop class="max-h-[85vh] w-full max-w-3xl">
            <div class="relative overflow-hidden rounded-2xl ring-1 ring-white/15">
                <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-brand-700 via-brand-900 to-ink">
                    <span class="font-display text-7xl font-bold text-white/10" x-text="items[i].year"></span>
                </div>
                <img x-show="items[i].src" :src="items[i].src" :alt="items[i].cap" onerror="this.style.display='none'"
                     class="absolute inset-0 h-full w-full object-contain bg-black">
            </div>
            <figcaption class="mt-4 text-center">
                <p class="font-display text-lg font-bold uppercase text-gold" x-text="items[i].year"></p>
                <p class="text-white/80" x-text="items[i].cap"></p>
            </figcaption>
        </figure>
    </div>
</section>
@endif

{{-- ============ EFSANELER ============ --}}
@if ($legends->isNotEmpty())
@php
    $legendItems = $legends->map(fn($l) => [
        'name' => $l->name,
        'role' => $l->role,
        'nickname' => $l->nickname,
        'era' => $l->era,
        'note' => $l->note,
        'bio' => $l->bio,
        'photo' => $l->imageUrl(),
        'initial' => mb_substr($l->name, 0, 1),
    ])->values();
@endphp
<section id="efsaneler" class="bg-brand-900/40 py-20"
         x-data="{ open:false, l:{}, items: {{ \Illuminate\Support\Js::from($legendItems) }}, show(n){ this.l=this.items[n]; this.open=true } }"
         @keydown.window.escape="open=false">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-12 text-center">
            <span class="text-sm font-bold uppercase tracking-widest text-gold">Ölümsüz İsimler</span>
            <h2 class="mt-2 font-display text-4xl font-bold uppercase text-white sm:text-5xl">Efsaneler</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-white/60">Adnan Süvari'nin altın neslinden, formayı şerefle taşıyan unutulmazlar. Detaylı yaşam öyküsü için bir isme dokun.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($legends as $idx => $p)
                <button type="button" @click="show({{ $idx }})"
                        class="group flex items-start gap-4 rounded-2xl border border-white/10 bg-ink/60 p-5 text-left transition hover:border-gold/40 hover:bg-ink/80 focus:outline-none focus:ring-2 focus:ring-gold">
                    <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-gold to-brand-600 font-display text-2xl font-bold text-ink ring-2 ring-white/10">
                        @if ($p->imageUrl())
                            <img src="{{ $p->imageUrl() }}" alt="{{ $p->name }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                        @else
                            {{ mb_substr($p->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-display text-lg font-bold uppercase text-white">{{ $p->name }}</p>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gold">{{ $p->role }}{{ $p->nickname ? ' · '.$p->nickname : '' }}</p>
                        @if ($p->note)<p class="mt-2 text-sm leading-relaxed text-white/70">{{ $p->note }}</p>@endif
                        <span class="mt-3 inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wide text-gold opacity-80 transition group-hover:opacity-100">
                            Detaylı İncele
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                        </span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Efsane popup --}}
    <div x-show="open" x-cloak @click="open=false" x-transition.opacity
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 p-4 backdrop-blur">
        <div @click.stop x-transition
             class="relative max-h-[88vh] w-full max-w-2xl overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-brand-900 to-ink shadow-2xl">
            <button @click="open=false" class="absolute right-3 top-3 z-10 grid h-10 w-10 place-items-center rounded-full bg-black/40 text-white backdrop-blur hover:bg-black/60">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <div class="max-h-[88vh] overflow-y-auto sm:flex">
                {{-- Foto --}}
                <div class="relative aspect-square w-full bg-gradient-to-br from-gold to-brand-600 sm:aspect-auto sm:w-2/5">
                    <div class="flex h-full min-h-[220px] items-center justify-center">
                        <span class="font-display text-8xl font-bold text-ink" x-show="!l.photo" x-text="l.initial"></span>
                    </div>
                    <img x-show="l.photo" :src="l.photo" :alt="l.name" class="absolute inset-0 h-full w-full object-cover">
                </div>
                {{-- Bilgi --}}
                <div class="p-6 sm:w-3/5 sm:p-7">
                    <template x-if="l.nickname">
                        <span class="inline-block rounded-full bg-gold/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-gold" x-text="l.nickname"></span>
                    </template>
                    <h3 class="mt-3 font-display text-3xl font-bold uppercase text-white" x-text="l.name"></h3>
                    <p class="mt-1 text-sm font-semibold uppercase tracking-wide text-gold">
                        <span x-text="l.role"></span><template x-if="l.era"><span> · <span x-text="l.era"></span></span></template>
                    </p>
                    <div class="mt-4 h-px bg-white/10"></div>
                    <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-white/80" x-text="l.bio || l.note"></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============ ONUR LİSTESİ ============ --}}
<section class="bg-ink py-20">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-12 text-center">
            <span class="text-sm font-bold uppercase tracking-widest text-gold">Müzemizden</span>
            <h2 class="mt-2 font-display text-4xl font-bold uppercase text-white sm:text-5xl">Onur Listesi</h2>
        </div>
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ($honours as $h)
                <div class="rounded-2xl border border-gold/20 bg-gradient-to-br from-brand-800/50 to-ink p-6 text-center">
                    <p class="font-display text-5xl font-bold text-gold">{{ $h['n'] }}</p>
                    <p class="mt-2 font-display text-base font-bold uppercase text-white">{{ $h['l'] }}</p>
                    <p class="mt-1 text-xs text-white/55">{{ $h['s'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ CTA ============ --}}
<section class="bg-gradient-to-br from-brand-700 to-brand-900 py-16">
    <div class="mx-auto max-w-3xl px-4 text-center">
        <h2 class="font-display text-3xl font-bold uppercase text-white sm:text-4xl">Bu Tarihin Bir Parçası Ol</h2>
        <p class="mx-auto mt-4 max-w-xl text-white/80">99 yıllık çınarın gölgesinde, geleceği birlikte yazıyoruz. Sarı-kırmızı ailesine sen de katıl.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}" class="rounded-lg bg-gold px-8 py-3 text-sm font-bold uppercase tracking-wide text-brand-800 hover:bg-gold-400">Üye Ol</a>
            <a href="{{ route('hakkimizda') }}" class="rounded-lg border border-white/25 px-8 py-3 text-sm font-bold uppercase tracking-wide text-white hover:bg-white/10">Hakkımızda</a>
        </div>
    </div>
</section>

@endsection
