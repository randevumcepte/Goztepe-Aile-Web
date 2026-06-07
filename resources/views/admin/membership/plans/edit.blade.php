@extends('layouts.admin')
@section('title', 'Plan Düzenle: '.$plan->name)

@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
@endphp

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('admin.membership.plans.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">← Planlar</a>

    <form method="POST" action="{{ route('admin.membership.plans.update', $plan) }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $label }}">Plan Adı</label>
                <input name="name" value="{{ old('name', $plan->name) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">Yıllık Fiyat (₺)</label>
                <input name="price" value="{{ old('price', $plan->price) }}" required class="{{ $input }}" placeholder="500">
            </div>
        </div>
        <div>
            <label class="{{ $label }}">Kısa Açıklama</label>
            <input name="description" value="{{ old('description', $plan->description) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Kart Maddeleri <span class="text-slate-400">(her satır bir madde)</span></label>
            <textarea name="card_features" rows="6" class="{{ $input }}">{{ old('card_features', implode("\n", $plan->card_features ?? [])) }}</textarea>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="{{ $label }}">Sıra</label>
                <input type="number" name="sort" value="{{ old('sort', $plan->sort) }}" min="0" class="{{ $input }}">
            </div>
            <label class="flex cursor-pointer items-center gap-2 self-end rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-gold has-[:checked]:bg-amber-50">
                <input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $plan->is_popular)) class="rounded text-brand-600"> En Popüler
            </label>
            <label class="flex cursor-pointer items-center gap-2 self-end rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active)) class="rounded text-brand-600"> Yayında
            </label>
        </div>
        <div class="flex gap-3 border-t border-slate-100 pt-5">
            <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Kaydet</button>
            <a href="{{ route('admin.membership.plans.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
        </div>
    </form>
</div>
@endsection
