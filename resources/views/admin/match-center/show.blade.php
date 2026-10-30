@extends('layouts.admin')
@section('title', 'Maç Merkezi')

@php
    $homeName = $fixture->is_home ? 'Göztepe' : $fixture->opponent;
    $awayName = $fixture->is_home ? $fixture->opponent : 'Göztepe';
    $card = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm';
    $h2 = 'text-base font-bold text-slate-900';
    $choiceLabels = ['evet' => 'Evet', 'hayir' => 'Hayır', 'bilmiyorum' => 'Bilmiyorum'];
    $outcomeLabels = ['goztepe_win' => 'Göztepe kazanır', 'draw' => 'Beraberlik', 'opponent_win' => $fixture->opponent.' kazanır'];
    $rem = $fixture->reminders_sent ?? [];
    $live = $fixture->live_state ?? [];
    $fmt = function ($v) {
        if (! $v) {
            return null;
        }
        try {
            return \Carbon\Carbon::parse($v)->locale('tr')->translatedFormat('d M Y · H:i');
        } catch (\Throwable $e) {
            return (string) $v;
        }
    };
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.match-center.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Maç Merkezi</a>
        <a href="{{ route('admin.fixtures.edit', $fixture) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Maçı düzenle</a>
    </div>

    {{-- Başlık --}}
    <div class="{{ $card }} flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs text-slate-500">{{ $fixture->kickoff_at?->locale('tr')->translatedFormat('d M Y · H:i') }} · {{ $fixture->competition }} · {{ $fixture->homeAwayLabel() }}</p>
            <p class="mt-1 text-xl font-extrabold text-slate-900">{{ $homeName }} <span class="text-slate-400">vs</span> {{ $awayName }}</p>
        </div>
        <div class="text-right">
            @if ($fixture->isPlayed())
                <p class="text-3xl font-extrabold text-slate-900">{{ $fixture->home_score }}-{{ $fixture->away_score }}</p>
                <p class="text-xs text-slate-500">Oynandı</p>
            @elseif ($fixture->isPredictionOpen())
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Tahminler açık</span>
            @else
                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Maç başladı / tahmin kapalı</span>
            @endif
        </div>
    </div>

    @unless ($installed)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Tahmin oyunu tabloları henüz kurulu değil (<code class="font-mono">php artisan migrate</code>). Tribün, lider notu, tahmin ve muhbir bölümleri kurulumdan sonra görünür.
        </div>
    @endunless

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- (a) AI analizi --}}
        <section class="{{ $card }}">
            <h2 class="{{ $h2 }}">AI Analizi</h2>
            @if ($analysis && ! empty($analysis['available']))
                <div class="mt-4">
                    @include('admin.match-center._probs', ['p' => $analysis['probabilities'], 'opponent' => $fixture->opponent])
                </div>
                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs text-slate-500">AI skor tahmini</dt>
                        <dd class="font-bold text-slate-900">{{ $homeName }} {{ $analysis['ai_pick']['home'] }}-{{ $analysis['ai_pick']['away'] }} {{ $awayName }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs text-slate-500">Beklenen gol</dt>
                        <dd class="font-bold text-slate-900">Göztepe {{ $analysis['expected_goals']['goztepe'] }} · {{ $fixture->opponent }} {{ $analysis['expected_goals']['opponent'] }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs text-slate-500">2,5 üst / Karşılıklı gol</dt>
                        <dd class="font-bold text-slate-900">%{{ $analysis['over_25'] ?? '—' }} / %{{ $analysis['btts'] ?? '—' }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs text-slate-500">Güven</dt>
                        <dd class="font-bold text-slate-900">{{ $analysis['confidence'] ?? '—' }}</dd>
                    </div>
                </dl>
                <div class="mt-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Olası skorlar</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($analysis['likely_scores'] ?? [] as $s)
                            <span class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm"><b>{{ $s['home'] }}-{{ $s['away'] }}</b> <span class="text-slate-500">%{{ $s['percent'] }}</span></span>
                        @endforeach
                    </div>
                </div>
                @if (! empty($analysis['preview']['text']))
                    <div class="mt-4 rounded-xl bg-brand-50 p-4 text-sm leading-relaxed text-slate-800">
                        <p class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-brand-700">Önizleme ({{ ($analysis['preview']['by'] ?? '') === 'ai' ? 'AI metni' : 'kural metni' }})</p>
                        {{ $analysis['preview']['text'] }}
                    </div>
                @endif
                @if (! empty($analysis['insights']))
                    <ul class="mt-4 list-inside list-disc space-y-1 text-sm text-slate-600">
                        @foreach ($analysis['insights'] as $line)
                            <li>{{ is_array($line) ? ($line['text'] ?? json_encode($line, JSON_UNESCAPED_UNICODE)) : $line }}</li>
                        @endforeach
                    </ul>
                @endif
                <p class="mt-4 text-[11px] text-slate-400">
                    Kaynak: {{ $analysis['source']['name'] ?? '—' }} · {{ $analysis['source']['matches'] ?? 0 }} maç
                    @if (! empty($analysis['model'])) · {{ $analysis['model'] }} @endif
                </p>
            @else
                @if ($analysis && ! empty($analysis['reason']) && ! $fixture->isPlayed())
                    <p class="mt-2 text-sm text-slate-500">{{ $analysis['reason'] }}</p>
                @endif
                @if ($fixture->ai_probs)
                    <p class="mt-3 text-xs text-slate-500">Maç başlamadan önce kaydedilen AI tahmini:</p>
                    <div class="mt-2">
                        @include('admin.match-center._probs', ['p' => $fixture->ai_probs, 'opponent' => $fixture->opponent])
                    </div>
                @endif
                @if ($fixture->ai_home_goals !== null)
                    <p class="mt-3 text-sm">AI skor tahmini: <b>{{ $homeName }} {{ $fixture->ai_home_goals }}-{{ $fixture->ai_away_goals }} {{ $awayName }}</b></p>
                @endif
                @if (! $fixture->ai_probs && $fixture->ai_home_goals === null)
                    <p class="mt-2 text-sm text-slate-400">Bu maç için kayıtlı AI tahmini yok.</p>
                @endif
            @endif
        </section>

        {{-- (b) Tribün --}}
        <section class="{{ $card }}">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="{{ $h2 }}">Tribün Ne Diyor</h2>
                @if ($installed)
                    <form method="POST" action="{{ route('admin.match-center.refresh-summary', $fixture) }}">
                        @csrf
                        <button class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Tribün özetini yeniden oluştur</button>
                    </form>
                @endif
            </div>

            @if (! $installed)
                <p class="mt-2 text-sm text-slate-400">Kurulu değil.</p>
            @elseif (! $crowd || empty($crowd['visible']) || ($crowd['count'] ?? 0) === 0)
                <p class="mt-3 text-sm text-slate-400">Henüz tahmin yapılmadı.</p>
            @else
                <p class="mt-1 text-xs text-slate-500">{{ $crowd['count'] }} tahmin</p>
                @if (! empty($crowd['distribution']))
                    <div class="mt-3 space-y-2">
                        @foreach ($outcomeLabels as $k => $lbl)
                            <div>
                                <div class="flex justify-between text-xs text-slate-600">
                                    <span>{{ $lbl }}</span>
                                    <span><b>%{{ $crowd['distribution'][$k] ?? 0 }}</b> · {{ $crowd['counts'][$k] ?? 0 }} kişi</span>
                                </div>
                                <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full {{ $k === 'goztepe_win' ? 'bg-brand-600' : ($k === 'draw' ? 'bg-slate-400' : 'bg-amber-400') }}" style="width: {{ (int) ($crowd['distribution'][$k] ?? 0) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">En popüler skor</p>
                        <p class="font-bold text-slate-900">
                            @if (! empty($crowd['popular_score']))
                                {{ $crowd['popular_score']['score'] }} <span class="text-xs font-normal text-slate-500">({{ $crowd['popular_score']['count'] }} kişi)</span>
                            @else — @endif
                        </p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Ağırlıklı (isabetliler önde)</p>
                        <p class="font-bold text-slate-900">
                            @if (! empty($crowd['weighted']))
                                %{{ $crowd['weighted']['goztepe_win'] }} / %{{ $crowd['weighted']['draw'] }} / %{{ $crowd['weighted']['opponent_win'] }}
                            @else — @endif
                        </p>
                    </div>
                </div>

                @if (! empty($crowd['top_factors']))
                    <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Öne çıkan etkenler</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($crowd['top_factors'] as $tf)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700">
                                {{ $tf['label'] }} · {{ $tf['count'] }}
                                @if ($tf['past_accuracy'] !== null) <span class="text-slate-400">(geçmiş isabet %{{ $tf['past_accuracy'] }})</span> @endif
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-700">
                    <p class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">Tribün özeti (doğrulanmamış)</p>
                    {{ $crowd['summary'] ?: 'Özet yok (en az 3 gerekçe/muhbir notu gerekir).' }}
                    @if ($fixture->crowd_summary_at)
                        <p class="mt-1 text-[11px] text-slate-400">AI özeti: {{ $fixture->crowd_summary_at->locale('tr')->translatedFormat('d M H:i') }}</p>
                    @endif
                </div>

                @if (! empty($crowd['blend']))
                    <div class="mt-4 rounded-xl border border-brand-200 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-brand-700">Birleşik tahmin (AI + tribün, tribün ağırlığı %{{ $crowd['blend']['crowd_weight'] }})</p>
                        <div class="mt-2">
                            @include('admin.match-center._probs', ['p' => $crowd['blend']['probabilities'], 'opponent' => $fixture->opponent])
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $crowd['blend']['explanation'] }}</p>
                    </div>
                @endif

                @if (! empty($crowd['result']))
                    <p class="mt-4 text-xs text-slate-600">
                        Tam skoru bilen: <b>{{ $crowd['result']['exact_count'] }}</b> ·
                        AI'ı yenenler: <b>{{ $crowd['result']['beat_ai_percent'] !== null ? '%'.$crowd['result']['beat_ai_percent'] : '—' }}</b>
                    </p>
                @endif
            @endif
        </section>
    </div>

    {{-- (c) Lider notu --}}
    @if ($installed)
        <section class="{{ $card }}">
            <h2 class="{{ $h2 }}">Lider Notu</h2>
            <p class="mt-1 text-xs text-slate-500">Tribün liderinin DOĞRULANMIŞ kadro/maç bilgisi. AI önizlemesi bunu kesin bilgi olarak kullanır.</p>
            <form method="POST" action="{{ route('admin.match-center.leader-note', $fixture) }}" class="mt-3 space-y-3">
                @csrf
                <textarea name="leader_note" rows="4" maxlength="2000" placeholder="Örn: Rakipte iki stoper cezalı, bizde kaptan sakatlıktan döndü."
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">{{ old('leader_note', $fixture->leader_note) }}</textarea>
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs text-slate-400">
                        @if ($fixture->leader_note_at) Son güncelleme: {{ $fixture->leader_note_at->locale('tr')->translatedFormat('d M Y · H:i') }} @else Henüz not yok. @endif
                    </p>
                    <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
                </div>
            </form>
        </section>

        {{-- (d) Tahminler --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="{{ $h2 }}">Tahminler <span class="text-sm font-normal text-slate-400">({{ $predictions->count() }})</span></h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Üye</th>
                            <th class="px-4 py-3">Tahmin</th>
                            <th class="px-4 py-3">Gerekçe</th>
                            <th class="px-4 py-3">Etkenler</th>
                            <th class="px-4 py-3 text-right">Puan</th>
                            <th class="px-4 py-3">AI'ı yendi mi</th>
                            <th class="px-4 py-3">Tarih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($predictions as $p)
                            <tr class="align-top hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $p->user?->name ?? 'Silinmiş üye' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $homeName }} <b>{{ $p->home_goals }}-{{ $p->away_goals }}</b> {{ $awayName }}</td>
                                <td class="max-w-xs px-4 py-3 text-slate-600">{{ $p->reason ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($p->factors ?? [] as $fk)
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">{{ $factors[$fk] ?? $fk }}</span>
                                        @empty
                                            <span class="text-slate-400">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-bold">{{ $p->points ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($p->beat_ai === true)
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Evet</span>
                                    @elseif ($p->beat_ai === false)
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Hayır</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $p->updated_at?->locale('tr')->translatedFormat('d M H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Bu maç için tahmin yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- (e) Muhbir cevapları --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="{{ $h2 }}">Tribün Muhbiri Cevapları <span class="text-sm font-normal text-slate-400">({{ $intel->count() }})</span></h2>
                <p class="text-xs text-slate-500">Doğrulanmamış üye bildirimleri.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                        <tr>
                            <th class="px-4 py-3 uppercase tracking-wide">Üye</th>
                            @foreach ($questions as $q)
                                <th class="px-4 py-3">{{ $q }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($intel as $i)
                            <tr class="align-top hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $i->user?->name ?? 'Silinmiş üye' }}</td>
                                @foreach ($questions as $qk => $q)
                                    @php $a = $i->answers[$qk] ?? null; @endphp
                                    <td class="px-4 py-3">
                                        @if ($a)
                                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ ($a['choice'] ?? '') === 'evet' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">{{ $choiceLabels[$a['choice'] ?? ''] ?? ($a['choice'] ?? '—') }}</span>
                                            @if (! empty($a['text']))
                                                <p class="mt-1 text-xs text-slate-600">{{ $a['text'] }}</p>
                                            @endif
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($questions) + 1 }}" class="px-4 py-10 text-center text-slate-400">Muhbir cevabı yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    {{-- (f) Canlı takip + hatırlatmalar --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="{{ $card }}">
            <h2 class="{{ $h2 }}">Canlı Takip</h2>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">ESPN maç kimliği</dt><dd class="font-mono">{{ $live['espn_id'] ?? '— (henüz eşleşmedi)' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Durum</dt>
                    <dd>
                        @if (! empty($live['done']))
                            <span class="rounded-full bg-slate-800 px-2 py-0.5 text-xs font-semibold text-white">Bitti</span>
                        @elseif (! empty($live['espn_id']))
                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700">Takipte</span>
                        @else
                            <span class="text-slate-400">Bekliyor</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between"><dt class="text-slate-500">Görülen olay sayısı</dt><dd class="font-semibold">{{ count($live['seen'] ?? []) }}</dd></div>
                <div>
                    <dt class="text-slate-500">Gönderilen bildirimler</dt>
                    <dd class="mt-1 flex flex-wrap gap-1">
                        @forelse ($live['sent'] ?? [] as $ev)
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700">{{ ['basladi' => 'Maç başladı', 'devre' => 'Devre arası'][$ev] ?? $ev }}</span>
                        @empty
                            <span class="text-slate-400">—</span>
                        @endforelse
                    </dd>
                </div>
            </dl>
        </section>

        <section class="{{ $card }}">
            <h2 class="{{ $h2 }}">Hatırlatmalar</h2>
            <dl class="mt-3 space-y-2 text-sm">
                @foreach (['acildi' => 'Tahmin açıldı', 'son_cagri' => 'Son çağrı', 'sonuc' => 'Sonuç'] as $rk => $rl)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">{{ $rl }}</dt>
                        <dd>
                            @if (! empty($rem[$rk]))
                                <span class="text-emerald-700">✓ {{ $fmt($rem[$rk]) }}</span>
                            @else
                                <span class="text-slate-400">gönderilmedi</span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </section>
    </div>
</div>
@endsection
