@extends('layouts.public')
@section('title', 'Taraftar Sesi & Haklarımız — Göztepe Tribünleri')

@section('content')
{{-- Başlık --}}
<section class="relative overflow-hidden border-b border-white/10 bg-gradient-to-br from-brand-700 to-ink">
    <div class="absolute inset-0 opacity-[0.06]" style="background-image:repeating-linear-gradient(45deg,#F7B500 0 3px,transparent 3px 18px)"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-16">
        <span class="text-sm font-bold uppercase tracking-widest text-gold">Taraftarın Sesi</span>
        <h1 class="mt-2 text-4xl font-bold uppercase text-white sm:text-5xl">Sesimiz & Haklarımız</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/80">Taraftar müşteri değildir; bu camianın sahibidir. Biz, Göztepe taraftarının ortak sesi ve haklarının takipçisiyiz.</p>
    </div>
</section>

{{-- Manifesto / ne için varız --}}
<section class="mx-auto max-w-3xl px-4 py-14 text-white/80 leading-relaxed">
    <h2 class="text-2xl font-bold uppercase text-white">Neden Varız?</h2>
    <p class="mt-4">Avrupa'da taraftar, örgütlendiğinde gerçek bir güce dönüşüyor. Liverpool taraftarı, bilet zammına karşı maçın 77. dakikasında tribünü terk edip fiyatları geri çektirdi. Almanya'da Dortmund taraftarı, koreografisini kendi parasıyla yapıp camianın kalbi oldu. Ortak nokta tek: <b class="text-white">bağımsızlık ve birlik.</b></p>
    <p class="mt-4">Göztepe Tribünleri de bu ruhla kuruldu: Tribünün gücünü tek çatı altında toplamak, taraftarın hakkını korumak ve her kuruşu şeffafça yönetmek.</p>

    <h2 class="mt-10 text-2xl font-bold uppercase text-white">Neyin Takipçisiyiz?</h2>
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach ([
            ['Adil bilet & kombine', 'Taraftarın stada erişimi; fahiş fiyatlara karşı ortak ses.'],
            ['Tribün kültürü', 'Koreografi, marş ve deplasman — taraftarın kendi gücüyle.'],
            ['Şeffaf hesap', 'Toplanan her kuruş faturasına kadar açık; gizli kapaklı yok.'],
            ['Camia dayanışması', 'Sosyal yardım ve birbirine destek; tribünün vicdanı.'],
        ] as [$t, $d])
            <div class="rounded-2xl bg-brand-800 p-5 ring-1 ring-white/10">
                <p class="font-display text-lg font-bold uppercase text-gold">{{ $t }}</p>
                <p class="mt-1 text-sm text-white/70">{{ $d }}</p>
            </div>
        @endforeach
    </div>

    <h2 class="mt-10 text-2xl font-bold uppercase text-white">İlkelerimiz</h2>
    <ul class="mt-4 space-y-2">
        <li>• Taraftar müşteri değil, camianın sahibidir.</li>
        <li>• Bağımsızız: gücümüzü taraftardan alırız.</li>
        <li>• Şeffafız: her gelir ve gider açıktır.</li>
        <li>• Birlikteyiz: tek ses, tek yürek.</li>
    </ul>

    <div class="mt-12 rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-8 text-center">
        <h3 class="text-2xl font-bold uppercase text-white">Sesimize Güç Kat</h3>
        <p class="mt-2 text-white/80">Ne kadar çoksak, sesimiz o kadar gür. Sen de aramıza katıl.</p>
        <a href="{{ route('register') }}" class="mt-5 inline-block rounded-lg bg-gold px-8 py-3 text-sm font-bold uppercase text-brand-800 hover:bg-gold-400">Üye Ol</a>
    </div>
</section>
@endsection
