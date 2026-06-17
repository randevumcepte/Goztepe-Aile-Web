@extends('layouts.member')
@section('title', 'Fikstür')

@section('content')
<div class="max-w-4xl">
    {{-- Sıradaki maçlar --}}
    <h2 class="text-lg font-bold mb-3">Sıradaki Maçlar</h2>
    @if ($upcoming->isNotEmpty())
        <div class="space-y-3 mb-8">
            @foreach ($upcoming as $f)
                <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            @if ($f->opponentLogoUrl())
                                <img src="{{ $f->opponentLogoUrl() }}" alt="" class="h-10 w-10 object-contain">
                            @else
                                <div class="grid h-10 w-10 place-items-center rounded-full bg-slate-100 text-slate-400 font-bold">VS</div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-bold truncate">Göztepe <span class="text-slate-400 font-normal">vs</span> {{ $f->opponent }}</p>
                                <p class="text-xs text-slate-500">{{ $f->competition }} · {{ $f->homeAwayLabel() }}</p>
                            </div>
                        </div>
                        <div class="text-right whitespace-nowrap">
                            <p class="font-bold text-brand-600">{{ $f->kickoff_at?->format('d.m.Y') }}</p>
                            <p class="text-sm text-slate-500">{{ $f->kickoff_at?->format('H:i') }}</p>
                        </div>
                    </div>
                    @if ($f->venue || $f->broadcast)
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500 border-t border-slate-100 pt-3">
                            @if ($f->venue)<span>📍 {{ $f->venue }}</span>@endif
                            @if ($f->broadcast)<span>📺 {{ $f->broadcast }}</span>@endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-xl border border-slate-200 bg-white px-5 py-8 text-center text-slate-400 mb-8">Yaklaşan maç yok.</div>
    @endif

    {{-- Oynanan maçlar --}}
    @if ($played->isNotEmpty())
        <h2 class="text-lg font-bold mb-3">Sonuçlar</h2>
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3">Maç</th>
                        <th class="px-4 py-3 text-center">Skor</th>
                        <th class="px-4 py-3">Kupa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($played as $f)
                        <tr>
                            <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $f->kickoff_at?->format('d.m.Y') }}</td>
                            <td class="px-4 py-3 font-medium">Göztepe <span class="text-slate-400">-</span> {{ $f->opponent }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $f->home_score }} - {{ $f->away_score }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $f->competition }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
