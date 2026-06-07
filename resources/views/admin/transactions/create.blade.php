@extends('layouts.admin')
@section('title', 'Gelir / Gider Ekle')

@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
@endphp

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('admin.transactions.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        Listeye dön
    </a>

    <form method="POST" action="{{ route('admin.transactions.store') }}" enctype="multipart/form-data"
          class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf

        <div>
            <h3 class="font-semibold text-slate-900">Hareket Bilgileri</h3>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $label }}">Kasa</label>
                    <select name="fund_id" class="{{ $input }}">
                        @foreach ($funds as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Yön</label>
                    <select name="direction" class="{{ $input }}">
                        @foreach ($directions as $d)<option value="{{ $d->value }}">{{ $d->label() }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Kategori</label>
                    <input name="category" list="kategoriler" value="{{ old('category') }}" required
                           class="{{ $input }}" placeholder="aidat, bağış, koreografi, deplasman…">
                    <datalist id="kategoriler">
                        <option value="aidat"><option value="bağış"><option value="sponsor">
                        <option value="satış"><option value="koreografi"><option value="deplasman"><option value="idari">
                    </datalist>
                </div>
                <div>
                    <label class="{{ $label }}">Tutar (₺)</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Tarih</label>
                    <input type="date" name="occurred_at" value="{{ old('occurred_at', date('Y-m-d')) }}" required class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Görünürlük</label>
                    <select name="visibility" class="{{ $input }}">
                        @foreach ($visibilities as $v)<option value="{{ $v->value }}">{{ $v->label() }}</option>@endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Açıklama</label>
                    <input name="description" value="{{ old('description') }}" class="{{ $input }}">
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-5">
            <h3 class="font-semibold text-slate-900">Fatura / Belge <span class="font-normal text-slate-400">(gider için önerilir)</span></h3>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $label }}">Maskeli Tedarikçi <span class="text-slate-400">(üyeye görünür)</span></label>
                    <input name="supplier_masked" value="{{ old('supplier_masked') }}" placeholder="B*** Tekstil" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Tam Tedarikçi <span class="text-slate-400">(yönetime özel)</span></label>
                    <input name="supplier_full" value="{{ old('supplier_full') }}" placeholder="Bayrak Tekstil Ltd." class="{{ $input }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Fatura Dosyası (PDF/JPG/PNG)</label>
                    <input type="file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="mt-1 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                </div>
            </div>
        </div>

        <div class="flex gap-3 border-t border-slate-100 pt-5">
            <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
            <a href="{{ route('admin.transactions.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
        </div>
    </form>
</div>
@endsection
