@extends('layouts.public')
@section('title', 'Şeffaf Kasa — Göztepe Tribünleri')

@php $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺'; @endphp

@section('content')
{{-- Başlık --}}
<section class="border-b border-white/10 bg-gradient-to-br from-brand-700 to-ink">
    <div class="mx-auto max-w-7xl px-4 py-14">
        <span class="text-sm font-bold uppercase tracking-widest text-gold">Hesap Verebilirlik</span>
        <h1 class="mt-2 text-4xl font-bold uppercase text-white sm:text-5xl">Şeffaf Kasa</h1>
        <p class="mt-3 max-w-2xl text-white/80">Aidat, bağış ve tüm harcamalar — faturalara kadar açık. Tüm rakamlar dernek fonundan canlı hesaplanır.</p>
    </div>
</section>

<div class="mx-auto max-w-7xl px-4 py-12">
    {{-- Özet --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
            <p class="text-sm font-semibold uppercase text-emerald-400">Toplam Gelir</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $tl($totals['gelir']) }}</p>
        </div>
        <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
            <p class="text-sm font-semibold uppercase text-rose-400">Toplam Gider</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $tl($totals['gider']) }}</p>
        </div>
        <div class="rounded-2xl bg-gold p-6">
            <p class="text-sm font-semibold uppercase text-brand-800">Güncel Bakiye</p>
            <p class="mt-2 text-3xl font-bold text-brand-900">{{ $tl($totals['bakiye']) }}</p>
        </div>
    </div>

    {{-- Kasalar --}}
    @if (count($funds))
        <h2 class="mb-3 mt-10 text-xl font-bold uppercase text-white">Kasalar</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($funds as $fund)
                <div class="rounded-2xl bg-brand-800 p-5 ring-1 ring-white/10">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gold">{{ $fund['name'] }}</h3>
                        <span class="text-lg font-bold text-white">{{ $tl($fund['balance']) }}</span>
                    </div>
                    @if ($fund['description'])<p class="mt-1 text-sm text-white/60">{{ $fund['description'] }}</p>@endif
                    <div class="mt-3 flex gap-4 text-sm">
                        <span class="text-emerald-400">+ {{ $tl($fund['gelir']) }}</span>
                        <span class="text-rose-400">− {{ $tl($fund['gider']) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Hareketler --}}
    <h2 class="mb-3 mt-10 text-xl font-bold uppercase text-white">Son Hareketler</h2>
    <div class="overflow-hidden rounded-2xl bg-brand-800 ring-1 ring-white/10">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/5 text-left text-xs uppercase tracking-wider text-white/60">
                        <th class="px-5 py-3 font-semibold">Tarih</th>
                        <th class="px-5 py-3 font-semibold">Açıklama</th>
                        <th class="px-5 py-3 font-semibold">Kategori</th>
                        <th class="px-5 py-3 text-right font-semibold">Tutar</th>
                        <th class="px-5 py-3 font-semibold">Fatura</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                @forelse ($transactions as $t)
                    <tr class="hover:bg-white/5">
                        <td class="whitespace-nowrap px-5 py-3 text-white/60">{{ $t->occurred_at?->format('d.m.Y') }}</td>
                        <td class="px-5 py-3 text-white">{{ $t->description ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="rounded-full bg-white/10 px-2.5 py-0.5 text-xs text-white/70">{{ $t->category }}</span></td>
                        <td class="px-5 py-3 text-right font-bold {{ $t->direction->value === 'gelir' ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $t->direction->value === 'gelir' ? '+' : '−' }} {{ $tl($t->amount) }}
                        </td>
                        <td class="px-5 py-3">
                            @if ($t->invoice)
                                <span class="text-xs text-gold" title="{{ $t->invoice->supplier_masked }}">📄 {{ $t->invoice->supplier_masked ?? 'Fatura' }}</span>
                            @else <span class="text-white/30">—</span> @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-white/40">Henüz hareket yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <p class="mt-3 text-xs text-white/40">Kişisel veriler KVKK gereği maskelenmiştir. Bakiye = gelir − gider, canlı hesaplanır.</p>
</div>
@endsection
