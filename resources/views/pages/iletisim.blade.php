@extends('layouts.public')
@section('title', 'İletişim — Göztepe Tribünleri')

@section('content')
<section class="border-b border-white/10 bg-gradient-to-br from-brand-700 to-ink">
    <div class="mx-auto max-w-7xl px-4 py-14">
        <h1 class="text-4xl font-bold uppercase text-white">İletişim</h1>
        <p class="mt-3 max-w-2xl text-white/80">Soruların, sponsorluk talepleri ve iş birlikleri için bize ulaş.</p>
    </div>
</section>

<div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 py-14 lg:grid-cols-3">
    {{-- İletişim bilgileri --}}
    <div class="space-y-4">
        @foreach (['Adres'=>'İzmir, Türkiye', 'E-posta'=>'info@goztepetribunleri.com', 'Sponsorluk'=>'sponsor@goztepetribunleri.com'] as $t=>$d)
            <div class="rounded-2xl bg-brand-800 p-5 ring-1 ring-white/10">
                <p class="text-xs font-bold uppercase tracking-wide text-gold">{{ $t }}</p>
                <p class="mt-1 text-white">{{ $d }}</p>
            </div>
        @endforeach
    </div>

    {{-- Form (şimdilik bilgilendirme) --}}
    <div class="lg:col-span-2">
        <form class="space-y-4 rounded-2xl bg-brand-800 p-6 ring-1 ring-white/10" onsubmit="return false">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <input placeholder="Ad Soyad" class="rounded-lg border border-white/10 bg-ink px-4 py-3 text-white placeholder-white/40 focus:border-gold focus:outline-none">
                <input placeholder="E-posta" class="rounded-lg border border-white/10 bg-ink px-4 py-3 text-white placeholder-white/40 focus:border-gold focus:outline-none">
            </div>
            <input placeholder="Konu" class="w-full rounded-lg border border-white/10 bg-ink px-4 py-3 text-white placeholder-white/40 focus:border-gold focus:outline-none">
            <textarea rows="5" placeholder="Mesajınız" class="w-full rounded-lg border border-white/10 bg-ink px-4 py-3 text-white placeholder-white/40 focus:border-gold focus:outline-none"></textarea>
            <button class="rounded-lg bg-gold px-6 py-3 text-sm font-bold uppercase text-brand-800 hover:bg-gold-400">Gönder</button>
            <p class="text-xs text-white/40">Not: Form gönderimi sonraki fazda aktive edilecek.</p>
        </form>
    </div>
</div>
@endsection
