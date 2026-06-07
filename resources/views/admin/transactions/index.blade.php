@extends('layouts.admin')
@section('title', 'Gelir / Gider')

@section('content')
@php $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺'; @endphp

<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-500">Tüm para hareketleri ve faturalar</p>
    <a href="{{ route('admin.transactions.create') }}"
       class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Kayıt
    </a>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3 font-semibold">Tarih</th>
                    <th class="px-5 py-3 font-semibold">Açıklama</th>
                    <th class="px-5 py-3 font-semibold">Kategori</th>
                    <th class="px-5 py-3 font-semibold">Kasa</th>
                    <th class="px-5 py-3 font-semibold">Görünürlük</th>
                    <th class="px-5 py-3 font-semibold">Fatura</th>
                    <th class="px-5 py-3 text-right font-semibold">Tutar</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($transactions as $t)
                <tr class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-5 py-3 text-slate-500">{{ $t->occurred_at?->format('d.m.Y') }}</td>
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $t->description ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $t->category }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-500">{{ $t->fund?->name }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs text-slate-500">{{ $t->visibility->label() }}</span>
                    </td>
                    <td class="px-5 py-3">
                        @if ($t->invoice?->file_path)
                            <a href="{{ asset('uploads/'.$t->invoice->file_path) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-brand-600 hover:underline">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                Gör
                            </a>
                        @elseif ($t->invoice)
                            <span class="text-slate-400" title="{{ $t->invoice->supplier_full }}">Belge</span>
                        @else <span class="text-slate-300">—</span> @endif
                    </td>
                    <td class="px-5 py-3 text-right font-bold {{ $t->direction->value === 'gelir' ? 'text-emerald-600' : 'text-brand-600' }}">
                        {{ $t->direction->value === 'gelir' ? '+' : '−' }} {{ $tl($t->amount) }}
                    </td>
                    <td class="px-5 py-3 text-right">
                        <form method="POST" action="{{ route('admin.transactions.destroy', $t) }}" onsubmit="return confirm('Bu kayıt silinsin mi?')">
                            @csrf @method('DELETE')
                            <button class="rounded-md p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600" title="Sil">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400">Kayıt yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
