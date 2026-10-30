@extends('layouts.admin')
@section('title', 'Göztepe Ayrıcalık — İşletmeler')

@php
    $tl = fn ($n) => number_format((float) $n, 2, ',', '.').' ₺';
    $statusBadge = [
        'aktif' => ['Aktif', 'bg-emerald-100 text-emerald-700'],
        'beklemede' => ['Başvuru', 'bg-amber-100 text-amber-800'],
        'pasif' => ['Pasif', 'bg-slate-200 text-slate-600'],
    ];
    $tabs = ['tumu' => 'Tümü', 'basvuru' => 'Başvurular', 'aktif' => 'Aktif', 'pasif' => 'Pasif'];
@endphp

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">Üyelere indirim ve damga kartı sunan anlaşmalı işletmeler</p>
    <div class="flex items-center gap-2">
        <a href="{{ url('/isletme') }}" target="_blank" class="hidden rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 sm:inline-flex">Okutma Sayfası</a>
        <a href="{{ route('admin.partners.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Yeni İşletme
        </a>
    </div>
</div>

{{-- Özet kartları --}}
<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Aktif İşletme</p>
        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $summary['active'] }}</p>
    </div>
    <div class="rounded-2xl border {{ $summary['pending'] ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-white' }} p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide {{ $summary['pending'] ? 'text-amber-700' : 'text-slate-500' }}">Bekleyen Başvuru</p>
        <p class="mt-1 text-2xl font-bold {{ $summary['pending'] ? 'text-amber-800' : 'text-slate-900' }}">{{ $summary['pending'] }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Bu Ay Ziyaret</p>
        <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($summary['visits'], 0, ',', '.') }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Bu Ay Kasaya Katkı</p>
        <p class="mt-1 text-2xl font-bold text-emerald-700">{{ $tl($summary['contribution']) }}</p>
    </div>
</div>

@if ($demoCount)
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4">
        <div>
            <p class="font-semibold text-amber-900">Örnek (demo) veriler yayında</p>
            <p class="text-sm text-amber-800">{{ $demoCount }} örnek işletme uygulamada görünüyor. Gerçek işletmeleri eklediğinizde bunları ve ziyaret kayıtlarını silin.</p>
        </div>
        <form method="POST" action="{{ route('admin.partners.demo-destroy') }}" onsubmit="return confirm('Tüm örnek (demo) işletmeler ve ziyaret kayıtları kalıcı olarak silinsin mi?')">
            @csrf @method('DELETE')
            <button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700">Örnek Verileri Sil</button>
        </form>
    </div>
@endif

{{-- Sekmeler --}}
<div class="mb-4 flex flex-wrap gap-2">
    @foreach ($tabs as $key => $label)
        <a href="{{ route('admin.partners.index', ['durum' => $key]) }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium {{ $durum === $key ? 'bg-slate-900 text-white' : 'border border-slate-300 bg-white text-slate-600 hover:bg-slate-50' }}">
            {{ $label }} <span class="ml-1 text-xs opacity-70">{{ $counts[$key] }}</span>
        </a>
    @endforeach
</div>

{{-- Bekleyen başvurular: her zaman üstte --}}
@if ($pending->isNotEmpty())
    <div class="mb-6 space-y-3">
        <h2 class="text-sm font-bold uppercase tracking-wide text-amber-700">Bekleyen Başvurular</h2>
        @foreach ($pending as $p)
            <div class="flex flex-wrap items-center gap-4 rounded-2xl border-2 border-amber-300 bg-amber-50 p-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-amber-200 text-lg font-bold text-amber-800">
                    @if ($p->logoUrl())<img src="{{ $p->logoUrl() }}" class="h-full w-full object-cover">@else{{ mb_substr($p->name, 0, 1) }}@endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-slate-900">{{ $p->name }} <span class="ml-1 text-xs font-medium text-slate-500">{{ $p->categoryLabel() }}</span></p>
                    <p class="text-sm text-amber-900">
                        Başvuran: <strong>{{ $p->applicant_name ?: '—' }}</strong>
                        @if ($p->applicant_phone) · <a href="tel:{{ $p->applicant_phone }}" class="underline">{{ $p->applicant_phone }}</a>@endif
                        · {{ $p->created_at?->format('d.m.Y H:i') }}
                    </p>
                    @if ($p->discount_text)<p class="text-xs text-slate-600">Teklif: {{ $p->discount_text }}</p>@endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.partners.edit', $p) }}" class="rounded-md bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700">İncele / Onayla</a>
                    <form method="POST" action="{{ route('admin.partners.destroy', $p) }}" onsubmit="return confirm('Başvuru silinsin mi?')">
                        @csrf @method('DELETE')
                        <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if ($durum !== 'basvuru')
<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">İşletme</th>
                <th class="px-4 py-3">İndirim</th>
                <th class="px-4 py-3">Durum</th>
                <th class="px-4 py-3">Kod / PIN</th>
                <th class="px-4 py-3 text-right">Bu Ay</th>
                <th class="px-4 py-3 text-right">İşlem</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($partners as $p)
                @php [$sLabel, $sClass] = $statusBadge[$p->status] ?? [$p->status, 'bg-slate-200 text-slate-600']; @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-lg bg-gradient-to-br from-amber-400 to-brand-600 font-bold text-white">
                                @if ($p->logoUrl())<img src="{{ $p->logoUrl() }}" class="h-full w-full object-cover">@else{{ mb_substr($p->name, 0, 1) }}@endif
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('admin.partners.show', $p) }}" class="font-semibold text-slate-800 hover:text-brand-600">{{ $p->name }}</a>
                                @if ($p->is_demo)<span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold uppercase text-amber-700">Demo</span>@endif
                                <p class="text-xs text-slate-500">{{ $p->categoryLabel() }}{{ $p->district ? ' · '.$p->district : '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-700">{{ $p->discount_text ?: '—' }}</td>
                    <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $sClass }}">{{ $sLabel }}</span></td>
                    <td class="px-4 py-3">
                        <span class="font-mono font-semibold text-slate-800">{{ $p->code }}</span>
                        @if ($p->pin_hash)
                            <span class="ml-1 text-xs text-emerald-600">PIN var</span>
                        @elseif ($p->status === 'aktif')
                            <span class="ml-1 rounded bg-rose-100 px-1.5 py-0.5 text-[11px] font-semibold text-rose-700" title="PIN olmadan işletme okutma sayfasına giriş yapamaz">PIN yok!</span>
                        @else
                            <span class="ml-1 text-xs text-slate-400">PIN yok</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <p class="font-semibold text-slate-800">{{ $p->month_visits }} ziyaret</p>
                        <p class="text-xs text-emerald-700">{{ $p->kasa_contribution ? $tl($p->month_contribution) : '—' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.partners.show', $p) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Detay</a>
                            <a href="{{ route('admin.partners.edit', $p) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Düzenle</a>
                            <form method="POST" action="{{ route('admin.partners.destroy', $p) }}" onsubmit="return confirm('İşletme ve tüm ziyaret kayıtları silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">Bu filtrede işletme yok. "Yeni İşletme" ile ekle.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@elseif ($pending->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">Bekleyen başvuru yok.</div>
@endif
@endsection
