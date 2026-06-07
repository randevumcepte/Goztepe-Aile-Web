@extends('layouts.public')
@section('title', 'Üyelik Avantajları — Göztepe Tribünleri')

@php
    $tiers = ['Öğrenci' => '250₺', 'Standart' => '500₺', 'Destekçi' => '1.000₺', 'VIP' => '2.500₺'];
    // Her hak için kademe görünürlüğü: true=✓, false=—, ya da metin (örn. "%5")
    $rows = [
        ['Dijital üye kartı (QR)', [true, true, true, true]],
        ['Şeffaf kasa erişimi', [true, true, true, true]],
        ['Etkinlik & duyuru bildirimleri', [true, true, true, true]],
        ['Kombine bekleme listesinde sıra', [true, true, true, true]],
        ['Maç biletinde öncelikli satış', [false, true, true, true]],
        ['Mağaza indirimi', ['%5', '%10', '%15', '%20']],
        ['Deplasman otobüsünde öncelik', [false, true, true, true]],
        ['Deplasman biletinde öncelik', [false, false, true, true]],
        ['Koreografi ekibine katılım önceliği', [false, false, true, true]],
        ['Sınırlı üretim ürünlere erken erişim', [false, false, true, true]],
        ['Hoş geldin paketi (atkı + rozet)', [false, false, true, true]],
        ['Doğum günü hediyesi', [false, false, true, true]],
        ['Oyuncu buluşması / imza günü', [false, false, false, true]],
        ['Etkinliklerde özel VIP alan', [false, false, false, true]],
        ['İsim plaketi / dijital onur duvarı', [false, false, false, true]],
    ];
@endphp

@section('content')
<section class="border-b border-white/10 bg-gradient-to-br from-brand-700 to-ink">
    <div class="mx-auto max-w-7xl px-4 py-14">
        <span class="text-sm font-bold uppercase tracking-widest text-gold">Üyelik</span>
        <h1 class="mt-2 text-4xl font-bold uppercase text-white sm:text-5xl">Üyelik Avantajları</h1>
        <p class="mt-3 max-w-2xl text-white/80">Her kademe bir altındakinin tüm haklarını kapsar. Sana uygun üyeliği seç, tribünün gücüne katıl.</p>
    </div>
</section>

<div class="mx-auto max-w-7xl px-4 py-12">
    <div class="overflow-hidden rounded-2xl ring-1 ring-white/10">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-700 text-white">
                        <th class="px-5 py-4 text-left font-bold uppercase">Avantaj</th>
                        @foreach ($tiers as $ad => $fiyat)
                            <th class="px-4 py-4 text-center {{ $ad === 'Standart' ? 'bg-gold text-brand-800' : '' }}">
                                <div class="font-display text-base font-bold uppercase">{{ $ad }}</div>
                                <div class="text-xs opacity-80">{{ $fiyat }}/yıl</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 bg-brand-900/40">
                    @foreach ($rows as [$hak, $degerler])
                        <tr class="hover:bg-white/5">
                            <td class="px-5 py-3 font-medium text-white/90">{{ $hak }}</td>
                            @foreach ($degerler as $i => $d)
                                <td class="px-4 py-3 text-center {{ $i === 1 ? 'bg-gold/5' : '' }}">
                                    @if ($d === true)
                                        <svg class="mx-auto h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    @elseif ($d === false)
                                        <span class="text-white/20">—</span>
                                    @else
                                        <span class="font-bold text-gold">{{ $d }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-brand-900">
                        <td class="px-5 py-4"></td>
                        @foreach ($tiers as $ad => $fiyat)
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('register') }}" class="inline-block rounded-lg bg-gold px-4 py-2 text-xs font-bold uppercase text-brand-800 hover:bg-gold-400">Üye Ol</a>
                            </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-8 rounded-2xl bg-brand-800/60 p-5 text-sm text-white/70 ring-1 ring-white/10">
        <p><b class="text-gold">Not:</b> Genel kurulda oy hakkı ve yönetime katılım, yalnız yönetimin atadığı <b>Asıl Üye</b> kademesine özeldir; halka açık üyelikte yer almaz.</p>
    </div>
</div>
@endsection
