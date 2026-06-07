@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $feature = $feature ?? null;
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div>
        <label class="{{ $label }}">Avantaj Adı</label>
        <input name="name" value="{{ old('name', $feature?->name) }}" required class="{{ $input }}" placeholder="Örn: Deplasman otobüsünde öncelik">
    </div>

    <div>
        <p class="{{ $label }}">Her plan için değer</p>
        <p class="mb-2 text-xs text-slate-400">Yaz: <b>var</b> (✓), <b>yok</b> (—) veya bir metin (örn. <b>%15</b>)</p>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ($plans as $pl)
                @php $cur = old('values.'.$pl->key, $feature?->valueFor($pl->key) ?? 'no'); @endphp
                <div>
                    <label class="text-xs font-semibold text-slate-600">{{ $pl->name }}</label>
                    <input name="values[{{ $pl->key }}]" value="{{ $cur }}" class="{{ $input }}" placeholder="var / yok / %15">
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="{{ $label }}">Sıra</label>
            <input type="number" name="sort" value="{{ old('sort', $feature?->sort ?? 0) }}" min="0" class="{{ $input }}">
        </div>
        <label class="flex cursor-pointer items-center gap-2 self-end rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $feature?->is_active ?? true)) class="rounded text-brand-600"> Tabloda göster
        </label>
    </div>

    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Kaydet</button>
        <a href="{{ route('admin.membership.features.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>
