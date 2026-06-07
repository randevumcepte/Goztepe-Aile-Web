@extends('layouts.public')
@section('title', 'Hakkımızda — Göztepe Tribünleri')

@section('content')
<div class="relative bg-ink">
    {{-- Arka plan resmi --}}
    <div class="pointer-events-none absolute inset-0 bg-cover bg-center"
         style="background-image:url('{{ asset('uploads/hakkimizda.jpg') }}')"></div>
    {{-- Siyah perde (karartma) --}}
    <div class="pointer-events-none absolute inset-0 bg-ink/90"></div>

    {{-- İçerik --}}
    <div class="relative">
{{-- Başlık şeridi --}}
<section class="relative overflow-hidden border-b border-white/10 bg-gradient-to-br from-brand-700/90 to-ink/80">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-6 sm:flex-row sm:items-center sm:justify-between sm:gap-8 sm:py-8">
        <div>
            <span class="text-sm font-bold uppercase tracking-widest text-gold">1925'ten Bugüne</span>
            <h1 class="mt-1 text-3xl font-bold uppercase text-white sm:text-4xl">Hakkımızda</h1>
        </div>
        <p class="max-w-md text-base text-white/80 sm:text-right">Göztepe Tribünleri; taraftarın gücünü tek çatı altında toplayan, şeffaf ve dayanışmacı bir taraftar derneğidir.</p>
    </div>
</section>

<section class="mx-auto max-w-3xl px-4 py-14 text-white/80 leading-relaxed">
    <h2 class="text-2xl font-bold uppercase text-white">Biz Kimiz?</h2>
    <p class="mt-4">Göztepe'nin en büyük gücü tribünleridir. Bu enerjiyi düzenli, şeffaf ve sürdürülebilir bir yapıya dönüştürmek için kurulduk. Amacımız; tribün koreografilerinden deplasman organizasyonlarına, sosyal yardımlardan camia dayanışmasına kadar tribünün ihtiyaçlarını taraftarın kendi gücüyle karşılamak.</p>

    <h2 class="mt-10 text-2xl font-bold uppercase text-white">Değerlerimiz</h2>
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach (['Şeffaflık'=>'Her kuruş, faturasına kadar açık.', 'Dayanışma'=>'Taraftarın gücü, taraftarın yanında.', 'Bağımsızlık'=>'Camia için, camia tarafından.'] as $t=>$d)
            <div class="rounded-2xl bg-brand-800 p-5 ring-1 ring-white/10">
                <p class="font-display text-lg font-bold uppercase text-gold">{{ $t }}</p>
                <p class="mt-1 text-sm text-white/70">{{ $d }}</p>
            </div>
        @endforeach
    </div>

    <h2 class="mt-10 text-2xl font-bold uppercase text-white">Ne Yapıyoruz?</h2>
    <ul class="mt-4 space-y-2">
        <li>• Tribün koreografisi üretimi</li>
        <li>• Deplasman organizasyonu ve desteği</li>
        <li>• Sosyal yardım ve dayanışma projeleri</li>
        <li>• Şeffaf kasa ile tam hesap verebilirlik</li>
    </ul>

    <div class="mt-12 rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-8 text-center">
        <h3 class="text-2xl font-bold uppercase text-white">Sen de Aramıza Katıl</h3>
        <a href="{{ route('register') }}" class="mt-5 inline-block rounded-lg bg-gold px-8 py-3 text-sm font-bold uppercase text-brand-800 hover:bg-gold-400">Üye Ol</a>
    </div>
</section>
    </div>{{-- /İçerik --}}
</div>{{-- /Arka plan sarıcı --}}
@endsection
