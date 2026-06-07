@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $event = $event ?? null;
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <label class="{{ $label }}">Yıl / Dönem</label>
            <input name="year" value="{{ old('year', $event?->year) }}" required placeholder="1968-69" class="{{ $input }}">
        </div>
        <div class="sm:col-span-2">
            <label class="{{ $label }}">Başlık</label>
            <input name="title" value="{{ old('title', $event?->title) }}" required placeholder="Avrupa'da Yarı Final" class="{{ $input }}">
        </div>
    </div>

    <div>
        <label class="{{ $label }}">Rozet (etiket)</label>
        <input name="tag" value="{{ old('tag', $event?->tag) }}" placeholder="Tarih Yazıldı" class="{{ $input }}">
        <p class="mt-1 text-xs text-slate-400">Kartın üstünde küçük renkli etiket olarak görünür.</p>
    </div>

    <div>
        <label class="{{ $label }}">Açıklama (zaman tüneli metni)</label>
        <textarea name="description" rows="4" class="{{ $input }}" placeholder="O döneme dair kısa anlatı…">{{ old('description', $event?->description) }}</textarea>
    </div>

    <div>
        <label class="{{ $label }}">Fotoğraf Başlığı (galeride görünür)</label>
        <input name="caption" value="{{ old('caption', $event?->caption) }}" placeholder="İlk Türkiye Kupası sevinci" class="{{ $input }}">
    </div>

    <div>
        <label class="{{ $label }}">Fotoğraf</label>
        @if ($event?->imageUrl())
            <img src="{{ $event->imageUrl() }}" class="mt-2 h-28 rounded-lg object-cover">
        @endif
        <input type="file" name="image" accept="image/*" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
        <p class="mt-1 text-xs text-slate-400">Önerilen oran 4:3 (örn. 1200×900). Boş bırakılırsa şık bir yıl-kapağı gösterilir.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Sıra</label>
            <input type="number" name="sort" value="{{ old('sort', $event?->sort ?? 0) }}" min="0" class="{{ $input }}">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
            <input type="checkbox" name="in_timeline" value="1" @checked(old('in_timeline', $event?->in_timeline ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Zaman tünelinde göster
        </label>
        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
            <input type="checkbox" name="in_gallery" value="1" @checked(old('in_gallery', $event?->in_gallery ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Galeride göster
        </label>
        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $event?->is_active ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Aktif
        </label>
    </div>

    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
        <a href="{{ route('admin.history.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>
