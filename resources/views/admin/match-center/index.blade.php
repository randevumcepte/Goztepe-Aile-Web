@extends('layouts.admin')
@section('title', 'Maç Merkezi')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-500">AI maç analizi, Tribün Tahmin Oyunu, muhbir cevapları, hatırlatmalar ve canlı takip tek ekranda.</p>

    @include('admin.match-center._status', ['status' => $status])

    @unless ($installed)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Tahmin oyunu tabloları henüz kurulu değil. Sunucuda <code class="font-mono">php artisan migrate</code> çalıştırılınca tahmin, muhbir ve sıralama verileri burada görünür.
        </div>
    @endunless

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            @foreach ([['Yaklaşan Maçlar', $upcoming, 'Yaklaşan maç yok. Fikstür / Maçlar ekranından maç ekleyin.'], ['Son Oynanan Maçlar', $played, 'Henüz skoru girilmiş maç yok.']] as [$heading, $list, $empty])
                <section>
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-500">{{ $heading }}</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @forelse ($list as $f)
                            @php
                                $p = $probs[$f->id] ?? null;
                                $rem = $f->reminders_sent ?? [];
                                $live = $f->live_state ?? [];
                            @endphp
                            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-xs text-slate-500">{{ $f->kickoff_at?->locale('tr')->translatedFormat('d M Y · H:i') }} · {{ $f->competition }}</p>
                                        <p class="mt-1 font-bold text-slate-900">
                                            @if ($f->is_home)
                                                Göztepe <span class="text-slate-400">vs</span> {{ $f->opponent }}
                                            @else
                                                {{ $f->opponent }} <span class="text-slate-400">vs</span> Göztepe
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $f->is_home ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-600' }}">{{ $f->homeAwayLabel() }}</span>
                                        @if ($f->isPlayed())
                                            <p class="mt-1 text-lg font-extrabold text-slate-900">{{ $f->home_score }}-{{ $f->away_score }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4">
                                    @if ($p)
                                        @include('admin.match-center._probs', ['p' => $p, 'opponent' => $f->opponent])
                                    @else
                                        <p class="text-xs text-slate-400">AI olasılığı yok (rakip için lig verisi bulunamadı).</p>
                                    @endif
                                </div>

                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600">
                                    <span>AI tahmini: <b>{{ $f->ai_home_goals !== null ? $f->ai_home_goals.'-'.$f->ai_away_goals : '—' }}</b></span>
                                    @if ($installed)
                                        <span>Tahmin: <b>{{ $f->predictions_count ?? 0 }}</b></span>
                                        <span>Muhbir: <b>{{ $f->intel_count ?? 0 }}</b></span>
                                    @endif
                                </div>

                                @if ($f->leader_note)
                                    <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-900"><b>Lider notu:</b> {{ \Illuminate\Support\Str::limit($f->leader_note, 110) }}</p>
                                @endif

                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach (['acildi' => 'Tahmin açıldı', 'son_cagri' => 'Son çağrı', 'sonuc' => 'Sonuç'] as $rk => $rl)
                                        <span class="rounded-full px-2 py-0.5 text-[11px] {{ ! empty($rem[$rk]) ? 'bg-emerald-100 font-semibold text-emerald-700' : 'bg-slate-100 text-slate-400' }}"
                                              title="{{ ! empty($rem[$rk]) ? $rem[$rk] : 'gönderilmedi' }}">
                                            {{ ! empty($rem[$rk]) ? '✓' : '·' }} {{ $rl }}
                                        </span>
                                    @endforeach
                                    @if (! empty($live['done']))
                                        <span class="rounded-full bg-slate-800 px-2 py-0.5 text-[11px] font-semibold text-white">Canlı: bitti</span>
                                    @elseif (! empty($live['espn_id']))
                                        <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-semibold text-rose-700">Canlı: takipte</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-400">Canlı: eşleşmedi</span>
                                    @endif
                                </div>

                                <div class="mt-4 flex justify-end">
                                    <a href="{{ route('admin.match-center.show', $f) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Detay</a>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-10 text-center text-sm text-slate-400 md:col-span-2">{{ $empty }}</div>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>

        <aside>
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="font-bold text-slate-900">Tribün Sıralaması</h2>
                    @if ($board)
                        <p class="text-xs text-slate-500">Sezon {{ $board['season'] }} · {{ $board['players'] }} oyuncu</p>
                    @endif
                </div>
                @if ($board)
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-2">#</th>
                                <th class="px-3 py-2">Üye</th>
                                <th class="px-3 py-2 text-right">Puan</th>
                                <th class="px-3 py-2 text-right">Tam</th>
                                <th class="px-3 py-2 text-right">AI'ı yendi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse (collect($board['rows'])->take(10) as $r)
                                <tr>
                                    <td class="px-3 py-2 font-semibold text-slate-500">{{ $r['rank'] }}</td>
                                    <td class="px-3 py-2 font-medium text-slate-800">{{ $r['name'] }}</td>
                                    <td class="px-3 py-2 text-right font-bold text-slate-900">{{ $r['points'] }}</td>
                                    <td class="px-3 py-2 text-right text-slate-600">{{ $r['exact'] }}</td>
                                    <td class="px-3 py-2 text-right text-slate-600">{{ $r['beat_ai'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-6 text-center text-slate-400">Bu sezon puanlanmış tahmin yok.</td></tr>
                            @endforelse
                            <tr class="bg-slate-50">
                                <td class="px-3 py-2 text-slate-400">🤖</td>
                                <td class="px-3 py-2 font-semibold text-slate-700">{{ $board['ai']['name'] }}</td>
                                <td class="px-3 py-2 text-right font-bold text-slate-900">{{ $board['ai']['points'] }}</td>
                                <td class="px-3 py-2 text-right text-slate-600">{{ $board['ai']['exact'] }}</td>
                                <td class="px-3 py-2 text-right text-slate-400">{{ $board['ai']['played'] }} maç</td>
                            </tr>
                        </tbody>
                    </table>
                    @php $l = $board['learning']; @endphp
                    <div class="border-t border-slate-100 px-5 py-4 text-xs leading-relaxed text-slate-600">
                        <p class="font-semibold text-slate-700">Öğrenme durumu</p>
                        <p class="mt-1">
                            Birleşik tahminde tribün ağırlığı <b>%{{ (int) round($l['crowd_weight'] * 100) }}</b>.
                            @if ($l['matches'] > 0)
                                Biten {{ $l['matches'] }} maçın {{ $l['crowd_better'] }}'inde tribün AI'dan isabetliydi.
                            @else
                                Henüz yeterli tahminli biten maç yok; başlangıç ağırlığı kullanılıyor.
                            @endif
                        </p>
                        <p class="mt-1 text-slate-400">{{ $board['scoring'] }}</p>
                    </div>
                @else
                    <p class="px-5 py-6 text-sm text-slate-400">Sıralama verisi yok.</p>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection
