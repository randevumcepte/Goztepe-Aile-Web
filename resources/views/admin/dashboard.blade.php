@extends('layouts.admin')
@section('title', 'Özet')

@section('content')
@php $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺'; @endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-neutral-200 p-5">
        <p class="text-sm text-neutral-500">Üye</p>
        <p class="text-2xl font-extrabold">{{ $stats['members'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-neutral-200 p-5">
        <p class="text-sm text-neutral-500">Aktif Üye</p>
        <p class="text-2xl font-extrabold">{{ $stats['active_members'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-neutral-200 p-5">
        <p class="text-sm text-neutral-500">Toplam Tahsilat</p>
        <p class="text-2xl font-extrabold text-emerald-700">{{ $tl($stats['payments_total']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-neutral-200 p-5">
        <p class="text-sm text-neutral-500">Kayıt Sayısı</p>
        <p class="text-2xl font-extrabold">{{ $stats['tx_count'] }}</p>
    </div>
</div>

<h2 class="font-bold mb-3">Kasalar</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    @foreach ($funds as $f)
        <div class="bg-white rounded-xl border border-neutral-200 p-5 flex justify-between items-center">
            <div>
                <p class="font-bold">{{ $f['name'] }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $f['is_public'] ? 'bg-emerald-100 text-emerald-700' : 'bg-neutral-200 text-neutral-600' }}">
                    {{ $f['is_public'] ? 'Üyeye açık' : 'Gizli' }}
                </span>
            </div>
            <p class="text-xl font-extrabold">{{ $tl($f['balance']) }}</p>
        </div>
    @endforeach
</div>

<div class="flex items-center justify-between mb-3">
    <h2 class="font-bold">Son Hareketler</h2>
    <a href="{{ route('admin.transactions.create') }}" class="bg-[#D5102E] text-white text-sm rounded-lg px-4 py-2">+ Gelir/Gider Ekle</a>
</div>
<div class="bg-white rounded-xl border border-neutral-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="bg-neutral-100 text-left">
            <th class="px-4 py-2">Tarih</th><th class="px-4 py-2">Açıklama</th>
            <th class="px-4 py-2">Kasa</th><th class="px-4 py-2 text-right">Tutar</th>
        </tr></thead>
        <tbody>
        @forelse ($recent as $t)
            <tr class="border-t border-neutral-100">
                <td class="px-4 py-2">{{ $t->occurred_at?->format('d.m.Y') }}</td>
                <td class="px-4 py-2">{{ $t->description ?? $t->category }}</td>
                <td class="px-4 py-2">{{ $t->fund?->name }}</td>
                <td class="px-4 py-2 text-right font-semibold {{ $t->direction->value === 'gelir' ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $t->direction->value === 'gelir' ? '+' : '−' }} {{ $tl($t->amount) }}
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-neutral-400">Kayıt yok.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
