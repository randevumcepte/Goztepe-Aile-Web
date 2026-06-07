@extends('layouts.admin')
@section('title', 'Gelir / Gider')

@section('content')
@php $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺'; @endphp

<div class="flex items-center justify-between mb-4">
    <h2 class="font-bold">Tüm Hareketler</h2>
    <a href="{{ route('admin.transactions.create') }}" class="bg-[#D5102E] text-white text-sm rounded-lg px-4 py-2">+ Yeni Kayıt</a>
</div>

<div class="bg-white rounded-xl border border-neutral-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="bg-neutral-100 text-left">
            <th class="px-4 py-2">Tarih</th><th class="px-4 py-2">Açıklama</th>
            <th class="px-4 py-2">Kategori</th><th class="px-4 py-2">Kasa</th>
            <th class="px-4 py-2">Görünürlük</th><th class="px-4 py-2">Fatura</th>
            <th class="px-4 py-2 text-right">Tutar</th><th class="px-4 py-2"></th>
        </tr></thead>
        <tbody>
        @forelse ($transactions as $t)
            <tr class="border-t border-neutral-100">
                <td class="px-4 py-2 whitespace-nowrap">{{ $t->occurred_at?->format('d.m.Y') }}</td>
                <td class="px-4 py-2">{{ $t->description ?? '—' }}</td>
                <td class="px-4 py-2">{{ $t->category }}</td>
                <td class="px-4 py-2">{{ $t->fund?->name }}</td>
                <td class="px-4 py-2"><span class="text-xs">{{ $t->visibility->label() }}</span></td>
                <td class="px-4 py-2">
                    @if ($t->invoice?->file_path)
                        <a href="{{ asset('storage/'.$t->invoice->file_path) }}" target="_blank" class="text-[#D5102E]">📄</a>
                    @elseif ($t->invoice)
                        <span title="{{ $t->invoice->supplier_full }}">📄</span>
                    @else — @endif
                </td>
                <td class="px-4 py-2 text-right font-semibold {{ $t->direction->value === 'gelir' ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $t->direction->value === 'gelir' ? '+' : '−' }} {{ $tl($t->amount) }}
                </td>
                <td class="px-4 py-2 text-right">
                    <form method="POST" action="{{ route('admin.transactions.destroy', $t) }}" onsubmit="return confirm('Silinsin mi?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-600">Sil</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-6 text-center text-neutral-400">Kayıt yok.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
