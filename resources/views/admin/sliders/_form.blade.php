@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $slider = $slider ?? null;
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div>
        <label class="{{ $label }}">Başlık</label>
        <input name="title" value="{{ old('title', $slider?->title) }}" required class="{{ $input }}">
    </div>
    <div>
        <label class="{{ $label }}">Alt Metin</label>
        <input name="subtitle" value="{{ old('subtitle', $slider?->subtitle) }}" class="{{ $input }}">
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Buton Metni</label>
            <input name="cta_label" value="{{ old('cta_label', $slider?->cta_label) }}" placeholder="Üye Ol" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Buton Linki</label>
            <input name="cta_url" value="{{ old('cta_url', $slider?->cta_url) }}" placeholder="/uye-ol veya https://…" class="{{ $input }}">
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Sıra</label>
            <input type="number" name="sort" value="{{ old('sort', $slider?->sort ?? 0) }}" min="0" class="{{ $input }}">
        </div>
        <div class="flex items-end">
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $slider?->is_active ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Aktif
            </label>
        </div>
    </div>
    <div>
        <label class="{{ $label }}">Görsel (geniş, yatay önerilir)</label>
        @if ($slider?->imageUrl())<img src="{{ $slider->imageUrl() }}" class="mt-2 h-28 rounded-lg object-cover">@endif
        <input type="file" name="image" accept="image/*" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
    </div>
    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
        <a href="{{ route('admin.sliders.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>
