@extends('layouts.admin')
@section('title', 'Site Ayarları')

@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
@endphp

@section('content')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        <div class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-900">Genel</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $label }}">Site Adı</label>
                    <input name="site_name" value="{{ old('site_name', $s['site_name'] ?? 'Göztepe Tribünleri') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Slogan</label>
                    <input name="slogan" value="{{ old('slogan', $s['slogan'] ?? 'İzmir\'in Gür Sesi') }}" class="{{ $input }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Üst Bar Metni</label>
                    <input name="topbar_text" value="{{ old('topbar_text', $s['topbar_text'] ?? 'İzmir\'in Gür Sesi · 1925') }}" class="{{ $input }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">Logo (PNG/SVG)</label>
                    @if (!empty($s['logo_path']))<img src="{{ asset('storage/'.$s['logo_path']) }}" class="mt-2 h-14">@endif
                    <input type="file" name="logo" accept="image/*" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                </div>
            </div>
        </div>

        <div class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-900">İletişim</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div><label class="{{ $label }}">Telefon</label><input name="phone" value="{{ old('phone', $s['phone'] ?? '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">E-posta</label><input name="email" value="{{ old('email', $s['email'] ?? '') }}" class="{{ $input }}"></div>
                <div class="sm:col-span-2"><label class="{{ $label }}">Adres</label><input name="address" value="{{ old('address', $s['address'] ?? 'İzmir, Türkiye') }}" class="{{ $input }}"></div>
            </div>
        </div>

        <div class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-900">Sosyal Medya</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div><label class="{{ $label }}">Instagram</label><input name="instagram_url" value="{{ old('instagram_url', $s['instagram_url'] ?? '') }}" placeholder="https://instagram.com/…" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">X (Twitter)</label><input name="x_url" value="{{ old('x_url', $s['x_url'] ?? '') }}" placeholder="https://x.com/…" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">YouTube</label><input name="youtube_url" value="{{ old('youtube_url', $s['youtube_url'] ?? '') }}" placeholder="https://youtube.com/…" class="{{ $input }}"></div>
            </div>
        </div>

        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
    </form>
</div>
@endsection
