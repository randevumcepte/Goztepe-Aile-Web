@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $group = $group ?? null;
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Grup adı</label>
            <input name="name" value="{{ old('name', $group?->name) }}" required class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Tribündeki yeri</label>
            <input name="tribune_area" value="{{ old('tribune_area', $group?->tribune_area) }}" placeholder="Örn: Kale arkası, C Blok" class="{{ $input }}">
        </div>
    </div>
    <div>
        <label class="{{ $label }}">Kısa tanıtım</label>
        <textarea name="description" rows="3" maxlength="500" class="{{ $input }}">{{ old('description', $group?->description) }}</textarea>
    </div>
    <div>
        <label class="{{ $label }}">Logo</label>
        @if ($group?->logoUrl())<img src="{{ $group->logoUrl() }}" class="mt-2 h-24 w-24 rounded-xl object-cover">@endif
        <input type="file" name="logo" accept="image/*" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
        <p class="mt-1 text-xs text-slate-400">Kare logo önerilir; üyenin Taraftar Kartı'nda görünür.</p>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Sıra</label>
            <input type="number" name="sort" value="{{ old('sort', $group?->sort ?? 0) }}" min="0" class="{{ $input }}">
        </div>
        <div class="flex items-end">
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $group?->is_active ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Aktif (uygulamada seçilebilir)
            </label>
        </div>
    </div>
    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
        <a href="{{ route('admin.fan-groups.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>
