@extends('layouts.admin')
@section('title', 'Unutulmazlar')

@section('content')
<div class="mb-4 flex items-center justify-between gap-3">
    <p class="text-sm text-slate-500">Mobil uygulamada <strong>Keşfet → Unutulmazlar</strong> bölümünde görünen futbolcular, teknik direktörler, maçlar ve tarihi anlar</p>
    <a href="{{ route('admin.memorables.create') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Unutulmaz
    </a>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($items as $m)
        @php $photoCount = count($m->photos()); @endphp
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-4 p-4">
                <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-amber-400 to-brand-600 text-2xl font-bold text-white">
                    @if ($m->coverUrl())<img src="{{ $m->coverUrl() }}" class="h-full w-full object-cover" loading="lazy">@else{{ mb_substr($m->name, 0, 1) }}@endif
                </div>
                <div class="min-w-0">
                    <h3 class="truncate font-semibold text-slate-800">{{ $m->name }}</h3>
                    <p class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ $m->categoryLabel() }}{{ $m->era ? ' · '.$m->era : '' }}</p>
                    @if ($m->subtitle)<p class="truncate text-xs text-slate-500">{{ $m->subtitle }}</p>@endif
                    <div class="mt-1 flex flex-wrap gap-1">
                        <span class="inline-block rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $m->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $m->is_active ? 'Aktif' : 'Pasif' }} · Sıra {{ $m->sort }}</span>
                        <span class="inline-block rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-semibold text-sky-700">{{ $photoCount }} fotoğraf</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 border-t border-slate-100 px-4 py-3">
                <a href="{{ route('admin.memorables.edit', $m) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Düzenle</a>
                <form method="POST" action="{{ route('admin.memorables.destroy', $m) }}" onsubmit="return confirm('Silinsin mi?')">
                    @csrf @method('DELETE')
                    <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
            Henüz unutulmaz yok. "Yeni Unutulmaz" ile ekle.
        </div>
    @endforelse
</div>
@endsection
