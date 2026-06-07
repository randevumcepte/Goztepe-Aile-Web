@extends('layouts.admin')
@section('title', 'Gelir / Gider Ekle')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.transactions.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-neutral-200 p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kasa</label>
                <select name="fund_id" class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                    @foreach ($funds as $f)
                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Yön</label>
                <select name="direction" class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                    @foreach ($directions as $d)
                        <option value="{{ $d->value }}">{{ $d->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <input name="category" list="kategoriler" value="{{ old('category') }}" required
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2" placeholder="aidat, bagis, tifo, deplasman...">
                <datalist id="kategoriler">
                    <option value="aidat"><option value="bagis"><option value="sponsor">
                    <option value="satis"><option value="tifo"><option value="deplasman"><option value="idari">
                </datalist>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tutar (₺)</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tarih</label>
                <input type="date" name="occurred_at" value="{{ old('occurred_at', date('Y-m-d')) }}" required
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Görünürlük</label>
                <select name="visibility" class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                    @foreach ($visibilities as $v)
                        <option value="{{ $v->value }}">{{ $v->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Açıklama</label>
            <input name="description" value="{{ old('description') }}"
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>

        <hr class="border-neutral-200">
        <p class="text-sm font-semibold text-neutral-600">Fatura / Belge (gider için önerilir)</p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Maskeli Tedarikçi (üyeye görünür)</label>
                <input name="supplier_masked" value="{{ old('supplier_masked') }}" placeholder="B*** Tekstil"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tam Tedarikçi (yönetime özel)</label>
                <input name="supplier_full" value="{{ old('supplier_full') }}" placeholder="Bayrak Tekstil Ltd."
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fatura Dosyası (PDF/JPG/PNG)</label>
            <input type="file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png"
                   class="w-full text-sm">
        </div>

        <div class="flex gap-3 pt-2">
            <button class="bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg px-6 py-2.5">Kaydet</button>
            <a href="{{ route('admin.transactions.index') }}" class="px-6 py-2.5 rounded-lg border border-neutral-300">İptal</a>
        </div>
    </form>
</div>
@endsection
