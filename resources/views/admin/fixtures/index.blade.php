@extends('layouts.admin')
@section('title', 'Fikstür / Maçlar')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-500">Ana sayfadaki "Haftanın Maçı" kartı buradan yönetilir. En yakın tarihli aktif maç gösterilir.</p>
    <a href="{{ route('admin.fixtures.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Maç
    </a>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">Tarih</th>
                <th class="px-4 py-3">Maç</th>
                <th class="px-4 py-3">Turnuva</th>
                <th class="px-4 py-3">Durum</th>
                <th class="px-4 py-3 text-right">İşlem</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($fixtures as $f)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-600">{{ $f->kickoff_at->locale('tr')->translatedFormat('d M Y · H:i') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">
                        @if ($f->is_home)
                            Göztepe <span class="text-slate-400">vs</span> {{ $f->opponent }}
                        @else
                            {{ $f->opponent }} <span class="text-slate-400">vs</span> Göztepe
                        @endif
                        @if ($f->isPlayed())
                            <span class="ml-1 rounded bg-slate-200 px-1.5 py-0.5 text-xs font-bold text-slate-700">{{ $f->home_score }}-{{ $f->away_score }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $f->competition }}</td>
                    <td class="px-4 py-3">
                        @if (! $f->is_active)
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Pasif</span>
                        @elseif ($f->isPlayed())
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">Oynandı</span>
                        @else
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Yaklaşan</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.fixtures.edit', $f) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Düzenle</a>
                            <form method="POST" action="{{ route('admin.fixtures.destroy', $f) }}" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">Henüz maç eklenmedi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
