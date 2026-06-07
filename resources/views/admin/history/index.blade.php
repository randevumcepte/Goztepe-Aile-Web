@extends('layouts.admin')
@section('title', 'Şanlı Tarihimiz')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-500">"Şanlı Tarihimiz" sayfasının zaman tüneli ve foto galerisi</p>
    <div class="flex items-center gap-2">
        <a href="{{ route('sanli-tarihimiz') }}" target="_blank" class="hidden rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 sm:inline-flex">Sayfayı Gör</a>
        <a href="{{ route('admin.history.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Yeni Kayıt
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($events as $e)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="relative aspect-[4/3] bg-gradient-to-br from-brand-700 via-brand-900 to-slate-900">
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="font-bold text-white/15" style="font-size:3rem">{{ $e->year }}</span>
                </div>
                @if ($e->imageUrl())<img src="{{ $e->imageUrl() }}" class="relative h-full w-full object-cover">@endif
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $e->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $e->is_active ? 'Aktif' : 'Pasif' }}</span>
                    <span class="text-xs text-slate-400">Sıra: {{ $e->sort }}</span>
                </div>
                <p class="mt-2 text-xs font-bold uppercase tracking-wide text-brand-600">{{ $e->year }}{{ $e->tag ? ' · '.$e->tag : '' }}</p>
                <h3 class="font-semibold text-slate-800">{{ $e->title }}</h3>
                <div class="mt-2 flex flex-wrap gap-1.5">
                    <span class="rounded-md px-2 py-0.5 text-[11px] font-medium {{ $e->in_timeline ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-400' }}">Zaman tüneli</span>
                    <span class="rounded-md px-2 py-0.5 text-[11px] font-medium {{ $e->in_gallery ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-400' }}">Galeri</span>
                </div>
                <div class="mt-3 flex gap-2">
                    <a href="{{ route('admin.history.edit', $e) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Düzenle</a>
                    <form method="POST" action="{{ route('admin.history.destroy', $e) }}" onsubmit="return confirm('Silinsin mi?')">
                        @csrf @method('DELETE')
                        <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
            Henüz kayıt yok. "Yeni Kayıt" ile ekle.
        </div>
    @endforelse
</div>
@endsection
