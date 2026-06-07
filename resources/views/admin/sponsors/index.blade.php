@extends('layouts.admin')
@section('title', 'Sponsorlar')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-500">Ana sayfada görünen sponsor logoları</p>
    <a href="{{ route('admin.sponsors.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Sponsor
    </a>
</div>

<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
    @forelse ($sponsors as $s)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid h-20 place-items-center rounded-lg bg-slate-50">
                @if ($s->logoUrl())<img src="{{ $s->logoUrl() }}" class="max-h-16 max-w-full object-contain">@else<span class="text-xs text-slate-400">logo yok</span>@endif
            </div>
            <p class="mt-2 truncate font-semibold text-slate-800">{{ $s->name }}</p>
            <p class="text-xs text-slate-500">{{ $s->tierLabel() }} · {{ $s->is_active ? 'Aktif' : 'Pasif' }}</p>
            <div class="mt-2 flex gap-2">
                <a href="{{ route('admin.sponsors.edit', $s) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Düzenle</a>
                <form method="POST" action="{{ route('admin.sponsors.destroy', $s) }}" onsubmit="return confirm('Silinsin mi?')">
                    @csrf @method('DELETE')
                    <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">Henüz sponsor yok.</div>
    @endforelse
</div>
@endsection
