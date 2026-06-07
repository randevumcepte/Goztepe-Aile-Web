@extends('layouts.admin')
@section('title', 'Yeni Kampanya')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.campaigns.store') }}"
          class="bg-white rounded-xl border border-neutral-200 p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tip</label>
                <select name="type" class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                    <option value="modal">Modal (pop-up)</option>
                    <option value="banner">Banner</option>
                    <option value="fullscreen">Tam ekran</option>
                    <option value="card">Kart</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Öncelik (0-100)</label>
                <input type="number" name="priority" value="{{ old('priority', 10) }}" min="0" max="100"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Başlık</label>
            <input name="title" value="{{ old('title') }}" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">İçerik</label>
            <textarea name="content" rows="3" class="w-full rounded-lg border border-neutral-300 px-3 py-2">{{ old('content') }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Buton Metni</label>
                <input name="cta_label" value="{{ old('cta_label') }}" placeholder="İncele"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Buton Linki</label>
                <input name="cta_url" value="{{ old('cta_url') }}" placeholder="https://..."
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kişiye max gösterim</label>
                <input type="number" name="frequency_cap" value="{{ old('frequency_cap', 3) }}" min="1" max="100"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Başlangıç</label>
                <input type="date" name="starts_at" value="{{ old('starts_at') }}"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Bitiş</label>
                <input type="date" name="ends_at" value="{{ old('ends_at') }}"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_commercial" value="1">
            Ticari (reklam) — yalnız ticari ileti rızası olanlara gösterilir
        </label>
        <button class="bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg px-6 py-2.5">Oluştur</button>
    </form>
</div>
@endsection
