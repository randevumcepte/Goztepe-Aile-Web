{{-- Olasılık çubuğu. Beklenen: $p (goztepe_win/draw/opponent_win yüzde), $opponent --}}
@php
    $gw = (int) ($p['goztepe_win'] ?? 0);
    $dr = (int) ($p['draw'] ?? 0);
    $ow = (int) ($p['opponent_win'] ?? 0);
@endphp
<div>
    <div class="flex h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
        <div class="bg-brand-600" style="width: {{ $gw }}%"></div>
        <div class="bg-slate-400" style="width: {{ $dr }}%"></div>
        <div class="bg-amber-400" style="width: {{ $ow }}%"></div>
    </div>
    <div class="mt-1 flex justify-between text-[11px] text-slate-600">
        <span><b class="text-brand-700">Göztepe %{{ $gw }}</b></span>
        <span>Beraberlik %{{ $dr }}</span>
        <span>{{ $opponent }} %{{ $ow }}</span>
    </div>
</div>
