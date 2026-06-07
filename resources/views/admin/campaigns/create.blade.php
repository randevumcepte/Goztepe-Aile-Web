@extends('layouts.admin')
@section('title', 'Yeni Kampanya')

@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
@endphp

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('admin.campaigns.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        Listeye dön
    </a>

    <form method="POST" action="{{ route('admin.campaigns.store') }}"
          class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $label }}">Tip</label>
                <select name="type" class="{{ $input }}">
                    <option value="modal">Modal (pop-up)</option>
                    <option value="banner">Banner</option>
                    <option value="fullscreen">Tam ekran</option>
                    <option value="card">Kart</option>
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Öncelik (0-100)</label>
                <input type="number" name="priority" value="{{ old('priority', 10) }}" min="0" max="100" class="{{ $input }}">
            </div>
        </div>
        <div>
            <label class="{{ $label }}">Başlık</label>
            <input name="title" value="{{ old('title') }}" required class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">İçerik</label>
            <textarea name="content" rows="3" class="{{ $input }}">{{ old('content') }}</textarea>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $label }}">Buton Metni</label>
                <input name="cta_label" value="{{ old('cta_label') }}" placeholder="İncele" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">Buton Linki</label>
                <input name="cta_url" value="{{ old('cta_url') }}" placeholder="https://…" class="{{ $input }}">
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="{{ $label }}">Kişiye max gösterim</label>
                <input type="number" name="frequency_cap" value="{{ old('frequency_cap', 3) }}" min="1" max="100" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">Başlangıç</label>
                <input type="date" name="starts_at" value="{{ old('starts_at') }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">Bitiş</label>
                <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="{{ $input }}">
            </div>
        </div>
        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-3 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
            <input type="checkbox" name="is_commercial" value="1" class="rounded text-brand-600 focus:ring-brand-500">
            Ticari (reklam) — yalnız ticari ileti rızası olanlara gösterilir
        </label>
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Oluştur</button>
    </form>
</div>
@endsection
