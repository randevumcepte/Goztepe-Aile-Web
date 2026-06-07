<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Şeffaf Kasa — Göztepe Tribünleri</title>
    {{-- Build gerektirmez (sunucu uyumu). Üretimde CI ile derlenmiş CSS'e geçilebilir. --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-50 text-neutral-900">
    @php
        $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺';
    @endphp

    {{-- Üst bant --}}
    <header class="bg-gradient-to-r from-[#D5102E] to-[#9B0B22] text-white">
        <div class="max-w-5xl mx-auto px-5 py-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-[#F7B500] text-[#9B0B22] font-black text-2xl grid place-items-center">G</div>
            <div>
                <h1 class="text-2xl font-extrabold leading-tight">Göztepe Tribünleri</h1>
                <p class="text-white/80 text-sm">Şeffaf Kasa — her kuruş açık</p>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-5 py-8">

        {{-- Özet kartları --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                <p class="text-sm text-emerald-700 font-semibold">Toplam Gelir</p>
                <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $tl($totals['gelir']) }}</p>
            </div>
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <p class="text-sm text-red-700 font-semibold">Toplam Gider</p>
                <p class="text-2xl font-extrabold text-red-700 mt-1">{{ $tl($totals['gider']) }}</p>
            </div>
            <div class="rounded-xl border border-neutral-800 bg-neutral-900 text-white p-5">
                <p class="text-sm text-[#F7B500] font-semibold">Güncel Bakiye</p>
                <p class="text-2xl font-extrabold mt-1">{{ $tl($totals['bakiye']) }}</p>
            </div>
        </section>

        {{-- Fonlar --}}
        @if (count($funds))
            <section class="mb-8">
                <h2 class="text-lg font-bold mb-3">Kasalar</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($funds as $fund)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-[#8a6500]">{{ $fund['name'] }}</h3>
                                <span class="text-lg font-extrabold text-[#8a6500]">{{ $tl($fund['balance']) }}</span>
                            </div>
                            @if ($fund['description'])
                                <p class="text-sm text-neutral-600 mt-1">{{ $fund['description'] }}</p>
                            @endif
                            <div class="flex gap-4 mt-3 text-sm">
                                <span class="text-emerald-700">+ {{ $tl($fund['gelir']) }}</span>
                                <span class="text-red-700">− {{ $tl($fund['gider']) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Hareketler --}}
        <section>
            <h2 class="text-lg font-bold mb-3">Son Hareketler</h2>
            <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[#D5102E] text-white text-left">
                            <th class="px-4 py-3">Tarih</th>
                            <th class="px-4 py-3">Açıklama</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3 text-right">Tutar</th>
                            <th class="px-4 py-3">Fatura</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                            <tr class="border-t border-neutral-100">
                                <td class="px-4 py-3 whitespace-nowrap text-neutral-600">{{ $t->occurred_at?->format('d.m.Y') }}</td>
                                <td class="px-4 py-3">{{ $t->description ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-700 text-xs">{{ $t->category }}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold {{ $t->direction->value === 'gelir' ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $t->direction->value === 'gelir' ? '+' : '−' }} {{ $tl($t->amount) }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($t->invoice)
                                        <span title="{{ $t->invoice->supplier_masked }}" class="text-xs text-[#9B0B22] font-medium">
                                            📄 {{ $t->invoice->supplier_masked ?? 'Fatura' }}
                                        </span>
                                    @else
                                        <span class="text-neutral-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-neutral-400">Henüz hareket yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-neutral-400 mt-3">
                Tüm rakamlar dernek fonundan canlı hesaplanır (gelir − gider). Kişisel veriler KVKK gereği maskelenmiştir.
            </p>
        </section>
    </main>

    <footer class="max-w-5xl mx-auto px-5 py-8 text-center text-xs text-neutral-400">
        Göztepe Tribünleri · Şeffaf Kasa
    </footer>
</body>
</html>
