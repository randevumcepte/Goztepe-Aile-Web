@extends('layouts.app')
@section('title', 'Panelim — Göztepe Tribünleri')

@section('content')
@php $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺'; @endphp

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-extrabold">Merhaba, {{ $user->name }}</h1>
    <a href="{{ route('uye.bildirimler') }}" class="relative text-sm bg-neutral-100 rounded-full px-4 py-2">
        🔔 Bildirimler
        @if ($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-[#D5102E] text-white text-xs rounded-full w-5 h-5 grid place-items-center">{{ $unreadCount }}</span>
        @endif
    </a>
</div>

{{-- Üyelik kartı --}}
<div class="rounded-xl bg-gradient-to-br from-[#D5102E] to-[#9B0B22] text-white p-6 mb-6">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-white/70 text-sm">Üye No</p>
            <p class="text-xl font-extrabold tracking-wide">{{ $member?->member_no ?? '—' }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-[#F7B500] text-[#9B0B22] font-black text-xl grid place-items-center">G</div>
    </div>
    <div class="mt-6 flex gap-6 text-sm">
        <div>
            <p class="text-white/70">Kategori</p>
            <p class="font-bold">{{ $member?->category?->label() ?? '—' }}</p>
        </div>
        <div>
            <p class="text-white/70">Durum</p>
            <p class="font-bold capitalize">{{ $member?->status ?? '—' }}</p>
        </div>
        <div>
            <p class="text-white/70">Oy Hakkı</p>
            <p class="font-bold">{{ $member?->hasVote() ? 'Var' : 'Yok' }}</p>
        </div>
    </div>
</div>

{{-- Hızlı işlemler --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <a href="{{ route('uye.aidat') }}" class="rounded-xl border border-neutral-200 bg-white p-5 hover:shadow">
        <p class="font-bold">💳 Aidat Öde</p>
        <p class="text-sm text-neutral-500 mt-1">Yıllık aidatını öde</p>
    </a>
    <a href="{{ route('uye.bagis') }}" class="rounded-xl border border-neutral-200 bg-white p-5 hover:shadow">
        <p class="font-bold">❤️ Bağış Yap</p>
        <p class="text-sm text-neutral-500 mt-1">Tribün fonuna destek ol</p>
    </a>
</div>

{{-- Ödeme geçmişi --}}
<h2 class="text-lg font-bold mb-3">Ödeme Geçmişim</h2>
<div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-neutral-100 text-left">
                <th class="px-4 py-3">Tarih</th>
                <th class="px-4 py-3">Tür</th>
                <th class="px-4 py-3 text-right">Tutar</th>
                <th class="px-4 py-3">Durum</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $p)
                <tr class="border-t border-neutral-100">
                    <td class="px-4 py-3 text-neutral-600">{{ $p->created_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3 capitalize">{{ $p->purpose }}</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ $tl($p->amount) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $p->status === 'basarili' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $p->status }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-neutral-400">Henüz ödeme yok.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
