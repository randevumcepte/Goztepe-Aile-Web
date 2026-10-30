@extends('layouts.admin')
@section('title', 'Bestelerimiz')

@section('content')
<div class="mb-4 flex items-center justify-between gap-3">
    <p class="text-sm text-slate-500">Mobil uygulamada <strong>Bestelerimiz</strong> bölümünde dinlenen tribün besteleri ve sözleri</p>
    <a href="{{ route('admin.chants.create') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Beste
    </a>
</div>

<div class="space-y-3">
    @forelse ($chants as $c)
        @php $firstLine = trim((string) strtok((string) $c->lyrics, "\r\n")); @endphp
        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:flex-row lg:items-center">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="font-semibold text-slate-800">{{ $c->title }}</h3>
                    <span class="inline-block rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $c->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $c->is_active ? 'Aktif' : 'Pasif' }} · Sıra {{ $c->sort }}</span>
                </div>
                @if ($c->note)<p class="text-xs font-semibold uppercase tracking-wide text-brand-600">{{ $c->note }}</p>@endif
                @if ($firstLine !== '')<p class="mt-1 truncate text-sm italic text-slate-500">“{{ $firstLine }}…”</p>@endif
            </div>
            <div class="shrink-0">
                @if ($c->audioUrl())
                    <audio controls preload="none" src="{{ $c->audioUrl() }}" class="h-10 w-full lg:w-72"></audio>
                @else
                    <span class="inline-block rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Ses yok</span>
                @endif
            </div>
            <div class="flex shrink-0 gap-2">
                <a href="{{ route('admin.chants.edit', $c) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Düzenle</a>
                <form method="POST" action="{{ route('admin.chants.destroy', $c) }}" onsubmit="return confirm('Silinsin mi?')">
                    @csrf @method('DELETE')
                    <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
            Henüz beste yok. "Yeni Beste" ile ekle.
        </div>
    @endforelse
</div>
@endsection
