@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $post = $post ?? null;
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div>
        <label class="{{ $label }}">Başlık</label>
        <input name="title" value="{{ old('title', $post?->title) }}" required class="{{ $input }}">
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Kategori</label>
            <select name="category" class="{{ $input }}">
                @foreach ($categories as $val => $lbl)
                    <option value="{{ $val }}" @selected(old('category', $post?->category) === $val)>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post?->is_published ?? true)) class="rounded text-brand-600 focus:ring-brand-500">
                Yayında
            </label>
        </div>
    </div>
    <div>
        <label class="{{ $label }}">Özet <span class="text-slate-400">(liste/önizleme)</span></label>
        <textarea name="excerpt" rows="2" class="{{ $input }}">{{ old('excerpt', $post?->excerpt) }}</textarea>
    </div>
    <div>
        <label class="{{ $label }}">İçerik</label>
        <textarea name="body" rows="10" required class="{{ $input }}">{{ old('body', $post?->body) }}</textarea>
    </div>
    <div>
        <label class="{{ $label }}">Kapak Görseli</label>
        @if ($post?->coverUrl())
            <img src="{{ $post->coverUrl() }}" class="mt-2 h-28 rounded-lg object-cover">
        @endif
        <input type="file" name="cover" accept="image/*"
               class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
    </div>
    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
        <a href="{{ route('admin.posts.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>
