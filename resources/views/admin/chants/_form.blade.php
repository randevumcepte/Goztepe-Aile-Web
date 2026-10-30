@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $chant = $chant ?? null;
    $isExternal = $chant?->audio_path && str_starts_with($chant->audio_path, 'http');
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Beste Adı</label>
            <input name="title" value="{{ old('title', $chant?->title) }}" required maxlength="160" placeholder="Göztepe’m" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Not</label>
            <input name="note" value="{{ old('note', $chant?->note) }}" maxlength="160" placeholder="Tribün klasiği · 1980’ler" class="{{ $input }}">
        </div>
    </div>

    <div>
        <label class="{{ $label }}">Sözler</label>
        <textarea name="lyrics" rows="10" maxlength="10000" class="{{ $input }}" placeholder="Bestenin sözleri…">{{ old('lyrics', $chant?->lyrics) }}</textarea>
    </div>

    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <h3 class="mb-2 text-sm font-semibold text-slate-800">Ses</h3>
        @if ($chant?->audioUrl())
            <div class="mb-3 flex flex-wrap items-center gap-3">
                <audio controls preload="none" src="{{ $chant->audioUrl() }}" class="h-10 w-full sm:w-80"></audio>
                <label class="flex items-center gap-2 text-xs text-rose-600">
                    <input type="checkbox" name="audio_remove" value="1" class="rounded text-rose-600 focus:ring-rose-500"> Sesi kaldır
                </label>
            </div>
        @endif

        <label class="{{ $label }}">Ses Dosyası Yükle</label>
        <input type="file" name="audio" accept="audio/*" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
        <p class="mt-1 text-xs text-slate-400">MP3, M4A, AAC, OGG veya WAV · en fazla 20 MB.</p>

        <div class="mt-4">
            <label class="{{ $label }}">veya Ses adresi (URL)</label>
            <input type="url" name="audio_url" value="{{ old('audio_url', $isExternal ? $chant->audio_path : '') }}" maxlength="500" placeholder="https://ornek.com/beste.mp3" class="{{ $input }}">
        </div>
        <p class="mt-2 text-xs text-amber-700">Sunucunun yükleme sınırı düşükse büyük dosyalar yüklenmeyebilir; o durumda ses adresi kullanın.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Sıra</label>
            <input type="number" name="sort" value="{{ old('sort', $chant?->sort ?? 0) }}" min="0" class="{{ $input }}">
        </div>
        <div class="flex items-end">
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $chant?->is_active ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Aktif
            </label>
        </div>
    </div>

    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
        <a href="{{ route('admin.chants.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>
