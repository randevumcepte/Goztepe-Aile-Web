@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $file = 'mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100';
    $memorable = $memorable ?? null;
    $statsText = collect($memorable?->stats ?? [])
        ->filter(fn ($s) => isset($s['label'], $s['value']))
        ->map(fn ($s) => $s['label'].': '.$s['value'])->join("\n");
    $gallery = collect($memorable?->gallery ?? [])->filter(fn ($p) => ! empty($p['url']));
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Kategori</label>
            <select name="category" required class="{{ $input }}">
                @foreach (\App\Models\Memorable::CATEGORIES as $key => $text)
                    <option value="{{ $key }}" @selected(old('category', $memorable?->category ?? 'futbolcu') === $key)>{{ $text }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">Ad / Başlık</label>
            <input name="name" value="{{ old('name', $memorable?->name) }}" required maxlength="160" placeholder="Rômulo Cardoso" class="{{ $input }}">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Alt Başlık</label>
            <input name="subtitle" value="{{ old('subtitle', $memorable?->subtitle) }}" maxlength="160" placeholder="Forvet · Brezilya" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Dönem</label>
            <input name="era" value="{{ old('era', $memorable?->era) }}" maxlength="60" placeholder="2024–2025" class="{{ $input }}">
        </div>
    </div>

    <div>
        <label class="{{ $label }}">Kart Özeti</label>
        <textarea name="summary" rows="2" maxlength="300" class="{{ $input }}" placeholder="Listede kartın altında görünen kısa özet…">{{ old('summary', $memorable?->summary) }}</textarea>
        <p class="mt-1 text-xs text-slate-400">En fazla 300 karakter.</p>
    </div>

    <div>
        <label class="{{ $label }}">Hikâyesi</label>
        <textarea name="bio" rows="12" maxlength="20000" class="{{ $input }}" placeholder="Detay sayfasında gösterilecek uzun hikâye…">{{ old('bio', $memorable?->bio) }}</textarea>
    </div>

    <div>
        <label class="{{ $label }}">İstatistikler</label>
        <textarea name="stats_text" rows="4" class="{{ $input }} font-mono" placeholder="Maç: 46&#10;Gol: 21&#10;Asist: 7">{{ old('stats_text', $statsText) }}</textarea>
        <p class="mt-1 text-xs text-slate-400">Her satır: <code>Etiket: değer</code></p>
    </div>

    <div>
        <label class="{{ $label }}">Kapak Fotoğrafı</label>
        @if ($memorable?->cover_path)
            <div class="mt-2 flex items-end gap-3">
                <img src="{{ \App\Models\Memorable::url($memorable->cover_path) }}" class="h-28 w-40 rounded-xl object-cover">
                <label class="flex items-center gap-2 text-xs text-rose-600">
                    <input type="checkbox" name="cover_remove" value="1" class="rounded text-rose-600 focus:ring-rose-500"> Kapağı kaldır
                </label>
            </div>
        @endif
        <input type="file" name="cover" accept="image/*" class="{{ $file }}">
        <p class="mt-1 text-xs text-slate-400">Kapak yoksa galerinin ilk fotoğrafı kullanılır. En fazla 6 MB.</p>
    </div>

    {{-- GALERİ --}}
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800">Fotoğraf Galerisi</h3>
            <span class="text-xs text-slate-500">{{ $gallery->count() }} fotoğraf</span>
        </div>

        @if ($gallery->isNotEmpty())
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach ($gallery as $i => $p)
                    <div class="flex gap-3 rounded-lg border border-slate-200 bg-white p-2 has-[.sil:checked]:border-rose-300 has-[.sil:checked]:bg-rose-50 has-[.sil:checked]:opacity-60">
                        <a href="{{ \App\Models\Memorable::url($p['url']) }}" target="_blank" class="shrink-0">
                            <img src="{{ \App\Models\Memorable::url($p['url']) }}" class="h-20 w-20 rounded-md object-cover" loading="lazy">
                        </a>
                        <div class="min-w-0 flex-1 space-y-1.5">
                            <input name="gallery_captions[{{ $i }}]" value="{{ old('gallery_captions.'.$i, $p['caption'] ?? '') }}" maxlength="255" placeholder="Açıklama" class="w-full rounded-md border border-slate-300 px-2 py-1 text-xs focus:border-brand-500 focus:outline-none">
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-1 text-xs text-slate-500">
                                    Sıra
                                    <input type="number" name="gallery_order[{{ $i }}]" value="{{ old('gallery_order.'.$i, $i) }}" class="w-16 rounded-md border border-slate-300 px-2 py-1 text-xs focus:border-brand-500 focus:outline-none">
                                </label>
                                <label class="flex items-center gap-1 text-xs font-medium text-rose-600">
                                    <input type="checkbox" name="gallery_delete[{{ $i }}]" value="1" @checked(old('gallery_delete.'.$i)) class="sil rounded text-rose-600 focus:ring-rose-500"> Sil
                                </label>
                            </div>
                            @if (str_starts_with($p['url'], 'http'))
                                <p class="truncate text-[11px] text-slate-400" title="{{ $p['url'] }}">Dış adres</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="mt-2 text-xs text-slate-400">Sıra numarası küçük olan önce gösterilir. "Sil" işaretlenen fotoğraflar kaydedince galeriden çıkar.</p>
        @else
            <p class="text-xs text-slate-400">Galeride henüz fotoğraf yok.</p>
        @endif

        <div class="mt-4">
            <label class="{{ $label }}">Fotoğraf Yükle (birden fazla seçilebilir)</label>
            <input type="file" name="gallery_new[]" multiple accept="image/*" class="{{ $file }}">
            <p class="mt-1 text-xs text-slate-400">Seçilen fotoğraflar galerinin sonuna eklenir. Her biri en fazla 6 MB.</p>
        </div>

        <div class="mt-4">
            <label class="{{ $label }}">Adresle fotoğraf ekle (her satır: adres | açıklama)</label>
            <textarea name="gallery_urls" rows="3" class="{{ $input }} font-mono text-xs" placeholder="https://ornek.com/foto.jpg | Gol sevinci">{{ old('gallery_urls') }}</textarea>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Sıra</label>
            <input type="number" name="sort" value="{{ old('sort', $memorable?->sort ?? 0) }}" min="0" class="{{ $input }}">
        </div>
        <div class="flex items-end">
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $memorable?->is_active ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Aktif
            </label>
        </div>
    </div>

    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
        <a href="{{ route('admin.memorables.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>
