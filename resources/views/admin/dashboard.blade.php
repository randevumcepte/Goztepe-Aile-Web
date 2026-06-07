@extends('layouts.admin')
@section('title', 'Özet')

@section('content')
@php $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺'; @endphp

{{-- İstatistik kartları --}}
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    @php
        $cards = [
            ['Toplam Üye', $stats['members'], 'bg-blue-50 text-blue-600', '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />'],
            ['Aktif Üye', $stats['active_members'], 'bg-emerald-50 text-emerald-600', '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />'],
            ['Toplam Tahsilat', $tl($stats['payments_total']), 'bg-amber-50 text-amber-600', '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />'],
            ['Kayıt Sayısı', $stats['tx_count'], 'bg-violet-50 text-violet-600', '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />'],
        ];
    @endphp
    @foreach ($cards as [$label, $value, $tone, $icon])
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">{{ $label }}</span>
                <span class="grid h-9 w-9 place-items-center rounded-xl {{ $tone }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">{!! $icon !!}</svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-extrabold text-slate-900">{{ $value }}</p>
        </div>
    @endforeach
</div>

{{-- Kasalar --}}
<h2 class="mt-8 mb-3 text-sm font-semibold uppercase tracking-wider text-slate-500">Kasalar</h2>
<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    @foreach ($funds as $f)
        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full {{ $f['is_public'] ? 'bg-emerald-100' : 'bg-slate-100' }}"></div>
            <div class="relative">
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-900">{{ $f['name'] }}</h3>
                    <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $f['is_public'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                        {{ $f['is_public'] ? 'Üyeye açık' : 'Gizli' }}
                    </span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $tl($f['balance']) }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- Son hareketler --}}
<div class="mt-8 flex items-center justify-between">
    <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Son Hareketler</h2>
    <a href="{{ route('admin.transactions.create') }}"
       class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Gelir / Gider Ekle
    </a>
</div>

<div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                <th class="px-5 py-3 font-semibold">Tarih</th>
                <th class="px-5 py-3 font-semibold">Açıklama</th>
                <th class="px-5 py-3 font-semibold">Kasa</th>
                <th class="px-5 py-3 text-right font-semibold">Tutar</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
        @forelse ($recent as $t)
            <tr class="hover:bg-slate-50">
                <td class="whitespace-nowrap px-5 py-3 text-slate-500">{{ $t->occurred_at?->format('d.m.Y') }}</td>
                <td class="px-5 py-3 font-medium text-slate-800">{{ $t->description ?? $t->category }}</td>
                <td class="px-5 py-3 text-slate-500">{{ $t->fund?->name }}</td>
                <td class="px-5 py-3 text-right font-semibold {{ $t->direction->value === 'gelir' ? 'text-emerald-600' : 'text-brand-600' }}">
                    {{ $t->direction->value === 'gelir' ? '+' : '−' }} {{ $tl($t->amount) }}
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-5 py-10 text-center text-slate-400">Henüz kayıt yok.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
