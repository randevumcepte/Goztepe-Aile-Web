@extends('layouts.member')
@section('title', 'Aidat Öde')

@section('content')
@php $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺'; @endphp
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-extrabold mb-6">Aidat Öde</h1>
    <form method="POST" action="{{ route('uye.odeme.start') }}" class="bg-white p-6 rounded-xl border border-neutral-200 space-y-4">
        @csrf
        <input type="hidden" name="purpose" value="aidat">
        <div>
            <p class="text-sm text-neutral-500">Kategorine göre yıllık aidat</p>
            <p class="text-3xl font-extrabold text-[#D5102E] mt-1">{{ $tl($amount) }}</p>
        </div>
        <input type="hidden" name="amount" value="{{ $amount }}">
        <button class="w-full bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg py-2.5">Ödemeye Geç</button>
        <p class="text-xs text-neutral-400 text-center">Güvenli ödeme sağlayıcısına yönlendirileceksin.</p>
    </form>
</div>
@endsection
