@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $legend = $legend ?? null;
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Ad Soyad</label>
            <input name="name" value="{{ old('name', $legend?->name) }}" required placeholder="Ali Artuner" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Mevki</label>
            <input name="role" value="{{ old('role', $legend?->role) }}" placeholder="Kaleci" class="{{ $input }}">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Lakap</label>
            <input name="nickname" value="{{ old('nickname', $legend?->nickname) }}" placeholder="Moskova Panteri" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Dönem</label>
            <input name="era" value="{{ old('era', $legend?->era) }}" placeholder="1961–1976" class="{{ $input }}">
        </div>
    </div>

    <div>
        <label class="{{ $label }}">Kart Kısa Metni</label>
        <input name="note" value="{{ old('note', $legend?->note) }}" placeholder="Türk kaleciliğinin zirvesi…" class="{{ $input }}">
        <p class="mt-1 text-xs text-slate-400">Kartın üstünde görünen tek satırlık özet.</p>
    </div>

    <div>
        <label class="{{ $label }}">Detaylı Biyografi (popup)</label>
        <textarea name="bio" rows="7" class="{{ $input }}" placeholder="Tıklandığında açılan pencerede gösterilecek detaylı yaşam öyküsü…">{{ old('bio', $legend?->bio) }}</textarea>
    </div>

    <div>
        <label class="{{ $label }}">Fotoğraf</label>
        @if ($legend?->imageUrl())
            <img src="{{ $legend->imageUrl() }}" class="mt-2 h-28 w-28 rounded-xl object-cover">
        @endif
        <input type="file" name="photo" accept="image/*" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
        <p class="mt-1 text-xs text-slate-400">Kare (1:1) fotoğraf önerilir. Boş bırakılırsa baş harfi gösterilir.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Sıra</label>
            <input type="number" name="sort" value="{{ old('sort', $legend?->sort ?? 0) }}" min="0" class="{{ $input }}">
        </div>
        <div class="flex items-end">
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $legend?->is_active ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Aktif
            </label>
        </div>
    </div>

    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
        <a href="{{ route('admin.legends.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>
