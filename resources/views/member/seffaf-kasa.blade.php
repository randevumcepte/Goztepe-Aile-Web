@extends('layouts.member')
@section('title', 'Şeffaf Kasa')

@php $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺'; @endphp

@section('content')
<div class="max-w-5xl">
    <p class="text-sm text-slate-500 mb-5">Aidat, bağış ve tüm harcamalar açık. Tüm rakamlar dernek fonundan canlı hesaplanır.</p>

    {{-- Özet --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-8">
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase text-emerald-600">Toplam Gelir</p>
            <p class="mt-2 text-2xl font-extrabold">{{ $tl($totals['gelir']) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase text-rose-600">Toplam Gider</p>
            <p class="mt-2 text-2xl font-extrabold">{{ $tl($totals['gider']) }}</p>
        </div>
        <div class="rounded-xl bg-gold p-5">
            <p class="text-xs font-semibold uppercase text-brand-800">Güncel Bakiye</p>
            <p class="mt-2 text-2xl font-extrabold text-brand-900">{{ $tl($totals['bakiye']) }}</p>
        </div>
    </div>

    {{-- Kasalar --}}
    @if (count($funds))
        <h2 class="text-lg font-bold mb-3">Kasalar</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-8">
            @foreach ($funds as $fund)
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-brand-700">{{ $fund['name'] }}</h3>
                        <span class="text-lg font-bold">{{ $tl($fund['balance']) }}</span>
                    </div>
                    @if ($fund['description'])<p class="mt-1 text-sm text-slate-500">{{ $fund['description'] }}</p>@endif
                    <div class="mt-3 flex gap-4 text-sm">
                        <span class="text-emerald-600">+ {{ $tl($fund['gelir']) }}</span>
                        <span class="text-rose-600">− {{ $tl($fund['gider']) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Hareketler --}}
    <h2 class="text-lg font-bold mb-3">Son Hareketler</h2>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">Tarih</th>
                    <th class="px-4 py-3">Açıklama</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3 text-right">Tutar</th>
                    <th class="px-4 py-3">Fatura</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($transactions as $t)
                <tr class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $t->occurred_at?->format('d.m.Y') }}</td>
                    <td class="px-4 py-3">{{ $t->description ?? '—' }}</td>
                    <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600">{{ $t->category }}</span></td>
                    <td class="px-4 py-3 text-right font-bold {{ $t->direction->value === 'gelir' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $t->direction->value === 'gelir' ? '+' : '−' }} {{ $tl($t->amount) }}
                    </td>
                    <td class="px-4 py-3">
                        @if ($t->invoice)
                            <span class="text-xs text-brand-600" title="{{ $t->invoice->supplier_masked }}">📄 {{ $t->invoice->supplier_masked ?? 'Fatura' }}</span>
                        @else <span class="text-slate-300">—</span> @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">Henüz hareket yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-xs text-slate-400">Kişisel veriler KVKK gereği maskelenmiştir. Bakiye = gelir − gider, canlı hesaplanır.</p>
</div>
@endsection
