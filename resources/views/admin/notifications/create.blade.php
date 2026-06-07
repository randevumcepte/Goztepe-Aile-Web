@extends('layouts.admin')
@section('title', 'Bildirim Gönder')

@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
@endphp

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('admin.notifications.send') }}"
          class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label class="{{ $label }}">Başlık</label>
            <input name="title" value="{{ old('title') }}" required class="{{ $input }}" placeholder="Örn: Bu hafta deplasman tribün buluşması">
        </div>
        <div>
            <label class="{{ $label }}">Mesaj</label>
            <textarea name="body" rows="3" class="{{ $input }}" placeholder="Bildirim metni…">{{ old('body') }}</textarea>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $label }}">Tür</label>
                <select name="type" class="{{ $input }}">
                    <option value="islemsel">İşlemsel (izinsiz gönderilebilir)</option>
                    <option value="ticari">Ticari (yalnız rıza verenlere)</option>
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Hedef Kategori</label>
                <select name="segment_kategori" class="{{ $input }}">
                    <option value="">Tüm üyeler</option>
                    @foreach ($categories as $c)<option value="{{ $c->value }}">{{ $c->label() }}</option>@endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="{{ $label }} mb-2">Kanallar</label>
            <div class="flex gap-3">
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                    <input type="checkbox" name="channels[]" value="in_app" checked class="rounded text-brand-600 focus:ring-brand-500"> Uygulama içi
                </label>
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                    <input type="checkbox" name="channels[]" value="push" checked class="rounded text-brand-600 focus:ring-brand-500"> Push
                </label>
            </div>
        </div>
        <div class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            Ticari (reklam/kampanya) bildirimler yalnız "ticari ileti rızası" veren üyelere gönderilir (İYS/KVKK).
        </div>
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Gönder</button>
    </form>
</div>
@endsection
