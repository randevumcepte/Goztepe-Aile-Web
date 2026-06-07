@extends('layouts.admin')
@section('title', 'Slider')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-500">Ana sayfa üst görsel/slayt yönetimi</p>
    <a href="{{ route('admin.sliders.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Slayt
    </a>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($sliders as $s)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="aspect-[16/9] bg-slate-100">
                @if ($s->imageUrl())<img src="{{ $s->imageUrl() }}" class="h-full w-full object-cover">@endif
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $s->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $s->is_active ? 'Aktif' : 'Pasif' }}</span>
                    <span class="text-xs text-slate-400">Sıra: {{ $s->sort }}</span>
                </div>
                <h3 class="mt-2 font-semibold text-slate-800">{{ $s->title }}</h3>
                @if ($s->subtitle)<p class="mt-1 text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($s->subtitle, 60) }}</p>@endif
                <div class="mt-3 flex gap-2">
                    <a href="{{ route('admin.sliders.edit', $s) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Düzenle</a>
                    <form method="POST" action="{{ route('admin.sliders.destroy', $s) }}" onsubmit="return confirm('Silinsin mi?')">
                        @csrf @method('DELETE')
                        <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
            Henüz slayt yok. "Yeni Slayt" ile ekle — eklemezsen ana sayfa öne çıkan haberi gösterir.
        </div>
    @endforelse
</div>
@endsection
