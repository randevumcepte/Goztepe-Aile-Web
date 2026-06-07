@extends('layouts.admin')
@section('title', 'Üyeler')

@section('content')
<form method="GET" class="mb-4 flex gap-2">
    <div class="relative flex-1 max-w-sm">
        <svg class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
        <input name="q" value="{{ request('q') }}" placeholder="Ad, e-posta veya üye no…"
               class="w-full rounded-lg border border-slate-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none">
    </div>
    <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900">Ara</button>
</form>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3 font-semibold">Üye</th>
                    <th class="px-5 py-3 font-semibold">Üye No</th>
                    <th class="px-5 py-3 font-semibold">Kategori &amp; Durum</th>
                    <th class="px-5 py-3 font-semibold">Oy</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($members as $m)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="grid h-9 w-9 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                                {{ strtoupper(substr($m->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $m->user?->name }}</p>
                                <p class="text-xs text-slate-500">{{ $m->user?->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $m->member_no }}</td>
                    <td class="px-5 py-3">
                        <form method="POST" action="{{ route('admin.members.update', $m) }}" class="flex flex-wrap items-center gap-2">
                            @csrf @method('PATCH')
                            <select name="category" class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs shadow-sm focus:outline-none">
                                @foreach ($categories as $c)<option value="{{ $c->value }}" @selected($m->category === $c)>{{ $c->label() }}</option>@endforeach
                            </select>
                            <select name="status" class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs shadow-sm focus:outline-none">
                                @foreach (['beklemede','aktif','pasif'] as $s)<option value="{{ $s }}" @selected($m->status === $s)>{{ ucfirst($s) }}</option>@endforeach
                            </select>
                            <button class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-900">Kaydet</button>
                        </form>
                    </td>
                    <td class="px-5 py-3">
                        @if ($m->hasVote())
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                Oy hakkı
                            </span>
                        @else <span class="text-xs text-slate-400">—</span> @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-slate-400">Üye bulunamadı.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $members->links() }}</div>
@endsection
