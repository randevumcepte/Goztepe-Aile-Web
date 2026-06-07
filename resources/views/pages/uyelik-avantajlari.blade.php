@extends('layouts.public')
@section('title', 'Üyelik Avantajları — Göztepe Tribünleri')

@section('content')
<section class="border-b border-white/10 bg-gradient-to-br from-brand-700 to-ink">
    <div class="mx-auto max-w-7xl px-4 py-14">
        <span class="text-sm font-bold uppercase tracking-widest text-gold">Üyelik</span>
        <h1 class="mt-2 text-4xl font-bold uppercase text-white sm:text-5xl">Üyelik Avantajları</h1>
        <p class="mt-3 max-w-2xl text-white/80">Her kademe bir altındakinin tüm haklarını kapsar. Sana uygun üyeliği seç, tribünün gücüne katıl.</p>
    </div>
</section>

<div class="mx-auto max-w-7xl px-4 py-12">
    @if ($plans->isEmpty())
        <div class="rounded-2xl bg-brand-800 p-12 text-center text-white/50 ring-1 ring-white/10">Üyelik planları henüz tanımlanmadı.</div>
    @else
    <div class="overflow-hidden rounded-2xl ring-1 ring-white/10">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-700 text-white">
                        <th class="px-5 py-4 text-left font-bold uppercase">Avantaj</th>
                        @foreach ($plans as $pl)
                            <th class="px-4 py-4 text-center {{ $pl->is_popular ? 'bg-gold text-brand-800' : '' }}">
                                <div class="font-display text-base font-bold uppercase">{{ $pl->name }}</div>
                                <div class="text-xs opacity-80">{{ $pl->price }}₺/yıl</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 bg-brand-900/40">
                    @foreach ($features as $f)
                        <tr class="hover:bg-white/5">
                            <td class="px-5 py-3 font-medium text-white/90">{{ $f->name }}</td>
                            @foreach ($plans as $pl)
                                @php $d = $f->valueFor($pl->key); @endphp
                                <td class="px-4 py-3 text-center {{ $pl->is_popular ? 'bg-gold/5' : '' }}">
                                    @if ($d === 'yes')
                                        <svg class="mx-auto h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    @elseif ($d === 'no')
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
                        @foreach ($plans as $pl)
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
    @endif
</div>
@endsection
