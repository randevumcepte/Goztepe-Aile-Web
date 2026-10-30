@extends('layouts.admin')
@section('title', 'Göztepe Ayrıcalık — '.$partner->name)

@php
    $tl = fn ($n) => $n === null ? '—' : number_format((float) $n, 2, ',', '.').' ₺';
    $statusBadge = [
        'aktif' => ['Aktif', 'bg-emerald-100 text-emerald-700'],
        'beklemede' => ['Başvuru bekliyor', 'bg-amber-100 text-amber-800'],
        'pasif' => ['Pasif', 'bg-slate-200 text-slate-600'],
    ];
    [$sLabel, $sClass] = $statusBadge[$partner->status] ?? [$partner->status, 'bg-slate-200 text-slate-600'];
    $periods = [
        'Bu Ay' => $thisMonth,
        'Geçen Ay' => $lastMonth,
        'Son 6 Ay' => $sixMonths,
    ];
@endphp

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.partners.index') }}" class="text-sm text-slate-500 hover:text-brand-600">← İşletmeler</a>
</div>

{{-- Başlık --}}
<div class="mb-6 flex flex-wrap items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-amber-400 to-brand-600 text-2xl font-bold text-white">
        @if ($partner->logoUrl())<img src="{{ $partner->logoUrl() }}" class="h-full w-full object-cover">@else{{ mb_substr($partner->name, 0, 1) }}@endif
    </div>
    <div class="min-w-0 flex-1">
        <h2 class="text-xl font-bold text-slate-900">{{ $partner->name }}
            @if ($partner->is_demo)<span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 align-middle text-[10px] font-bold uppercase text-amber-700">Demo</span>@endif
        </h2>
        <p class="text-sm text-slate-500">{{ $partner->categoryLabel() }}{{ $partner->district ? ' · '.$partner->district : '' }}{{ $partner->discount_text ? ' · '.$partner->discount_text : '' }}</p>
        <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $sClass }}">{{ $sLabel }}</span>
        @if ($partner->hasStampCard())
            <span class="mt-1 inline-block rounded-full bg-brand-50 px-2 py-0.5 text-[11px] font-semibold text-brand-700">Damga kartı: {{ $partner->stamp_goal }} ziyarette {{ $partner->stamp_reward }}</span>
        @endif
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.partners.edit', $partner) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Düzenle</a>
        <form method="POST" action="{{ route('admin.partners.destroy', $partner) }}" onsubmit="return confirm('İşletme ve tüm ziyaret kayıtları silinsin mi?')">
            @csrf @method('DELETE')
            <button class="rounded-lg px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50">Sil</button>
        </form>
    </div>
</div>

@if ($partner->status === 'beklemede' && ($partner->applicant_name || $partner->applicant_phone))
    <div class="mb-6 rounded-2xl border-2 border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
        <strong>Başvuru:</strong> {{ $partner->applicant_name ?: '—' }}
        @if ($partner->applicant_phone) · <a href="tel:{{ $partner->applicant_phone }}" class="underline">{{ $partner->applicant_phone }}</a>@endif
        — Onaylamak için <a href="{{ route('admin.partners.edit', $partner) }}" class="font-semibold underline">düzenleyip</a> durumu "Aktif" yapın ve PIN belirleyin.
    </div>
@endif

{{-- Okutma bilgileri --}}
<div class="mb-6 rounded-2xl border-2 border-brand-200 bg-brand-50/50 p-6">
    <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-brand-700">İşletme okutma bilgileri</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <p class="text-xs font-semibold text-slate-500">Adres</p>
            <a href="{{ $scanUrl }}" target="_blank" class="break-all font-mono text-base font-semibold text-slate-900 hover:text-brand-600">{{ $scanUrl }}</a>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500">İşletme Kodu</p>
            <p class="font-mono text-4xl font-black tracking-[0.2em] text-slate-900">{{ $partner->code }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500">PIN</p>
            @if ($partner->pin_hash)
                <p class="text-base font-semibold text-emerald-700">Tanımlı ✓</p>
                <p class="text-xs text-slate-500">Güvenlik için gösterilmez; unutulursa düzenleyip yenisini verin.</p>
            @else
                <p class="text-base font-semibold text-rose-600">Tanımlı değil</p>
                <p class="text-xs text-rose-600">İşletme giriş yapamaz. <a href="{{ route('admin.partners.edit', $partner) }}" class="underline">PIN belirle</a></p>
            @endif
        </div>
    </div>
    <div class="mt-5">
        <p class="mb-1 text-xs font-semibold text-slate-500">İşletmeye gönderilecek mesaj</p>
        <textarea id="gt-msg" readonly rows="5" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">{{ $message }}</textarea>
        <div class="mt-2 flex items-center gap-3">
            <button type="button" onclick="gtCopy()" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Mesajı Kopyala</button>
            <span id="gt-copied" class="hidden text-sm font-medium text-emerald-700">Kopyalandı ✓</span>
            <span class="text-xs text-slate-400">PIN'i işletmeye ayrıca (telefonla) iletin.</span>
        </div>
    </div>
</div>

{{-- İstatistikler --}}
<div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
    @foreach ($periods as $title => $s)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">{{ $title }}</p>
            <dl class="grid grid-cols-2 gap-3">
                <div><dt class="text-xs text-slate-500">Ziyaret</dt><dd class="text-xl font-bold text-slate-900">{{ $s['visits'] }}</dd></div>
                <div><dt class="text-xs text-slate-500">Farklı üye</dt><dd class="text-xl font-bold text-slate-900">{{ $s['members'] }}</dd></div>
                <div><dt class="text-xs text-slate-500">Hediye</dt><dd class="text-xl font-bold text-slate-900">{{ $s['rewards'] }}</dd></div>
                <div><dt class="text-xs text-slate-500">Kasaya katkı</dt><dd class="text-xl font-bold text-emerald-700">{{ $tl($s['contribution']) }}</dd></div>
            </dl>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Aylık ziyaret --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-500">Son 6 Ay — Aylık Ziyaret</p>
        <div class="space-y-3">
            @foreach ($months as $m)
                <div>
                    <div class="mb-1 flex justify-between text-xs"><span class="text-slate-600">{{ $m['label'] }}</span><span class="font-semibold text-slate-800">{{ $m['count'] }}</span></div>
                    <div class="h-2.5 w-full rounded-full bg-slate-100">
                        <div class="h-2.5 rounded-full bg-brand-600" style="width: {{ $m['count'] ? max(3, round($m['count'] / $maxMonth * 100)) : 0 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Son ziyaretler --}}
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
        <p class="border-b border-slate-100 px-5 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">Son 50 Okutma</p>
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-2">Tarih</th><th class="px-4 py-2">Üye</th><th class="px-4 py-2">Tür</th><th class="px-4 py-2">Damga</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($visits as $v)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-2 text-slate-600">{{ $v->created_at?->format('d.m.Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <span class="font-medium text-slate-800">{{ $v->user?->name ?? 'Silinmiş üye' }}</span>
                            @if ($v->user?->member?->member_no)<span class="ml-1 font-mono text-xs text-slate-400">#{{ $v->user->member->member_no }}</span>@endif
                        </td>
                        <td class="px-4 py-2">
                            @if ($v->type === 'hediye')
                                <span class="rounded-full bg-brand-100 px-2 py-0.5 text-[11px] font-semibold text-brand-700">Hediye</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">Ziyaret</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-xs">{!! $v->stamped ? '<span class="font-semibold text-emerald-700">Evet</span>' : '<span class="text-slate-400">Hayır</span>' !!}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-slate-400">Henüz okutma yok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function gtCopy() {
        const t = document.getElementById('gt-msg');
        const done = () => { const s = document.getElementById('gt-copied'); s.classList.remove('hidden'); setTimeout(() => s.classList.add('hidden'), 2000); };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(t.value).then(done);
        } else {
            t.select(); document.execCommand('copy'); done();
        }
    }
</script>
@endsection
