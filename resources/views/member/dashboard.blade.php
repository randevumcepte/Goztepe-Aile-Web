@extends('layouts.app')
@section('title', 'Panelim — Göztepe Tribünleri')

@section('content')
@php $tl = fn ($v) => number_format((float) $v, 2, ',', '.') . ' ₺'; @endphp

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-extrabold">Merhaba, {{ $user->name }}</h1>
    <a href="{{ route('uye.bildirimler') }}" class="relative text-sm bg-neutral-100 rounded-full px-4 py-2">
        🔔 Bildirimler
        @if ($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-[#D5102E] text-white text-xs rounded-full w-5 h-5 grid place-items-center">{{ $unreadCount }}</span>
        @endif
    </a>
</div>

{{-- Dijital üye kartı --}}
<div class="rounded-xl bg-gradient-to-br from-[#D5102E] to-[#9B0B22] text-white p-6 mb-6">
    <div class="flex justify-between items-start gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="" class="h-8 w-8 rounded-lg object-contain">
                <span class="text-sm font-bold tracking-wide text-white/90">GÖZTEPE TRİBÜNLERİ</span>
            </div>
            <p class="text-white/70 text-sm mt-5">Üye No</p>
            <p class="text-xl font-extrabold tracking-wide">{{ $member?->member_no ?? '—' }}</p>
            <p class="text-white/90 font-semibold mt-1">{{ $user->name }}</p>

            <div class="mt-5 flex gap-6 text-sm">
                <div>
                    <p class="text-white/70">Kategori</p>
                    <p class="font-bold">{{ $member?->category?->label() ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-white/70">Durum</p>
                    <p class="font-bold capitalize">{{ $member?->status ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-white/70">Oy Hakkı</p>
                    <p class="font-bold">{{ $member?->hasVote() ? 'Var' : 'Yok' }}</p>
                </div>
            </div>
        </div>

        @if ($member?->member_no)
            <div class="text-center">
                <div id="member-qr" class="bg-white p-2 rounded-lg inline-block"
                     data-value="GT-UYE:{{ $member->member_no }}"></div>
                <p class="text-[11px] text-white/70 mt-2">Girişte okut</p>
            </div>
        @else
            <div class="w-10 h-10 rounded-full bg-[#F7B500] text-[#9B0B22] font-black text-xl grid place-items-center">G</div>
        @endif
    </div>
</div>

{{-- Katkı özeti --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <p class="text-sm text-neutral-500">Bu yılki katkın</p>
        <p class="text-2xl font-extrabold mt-1">{{ $tl($thisYearContribution) }}</p>
    </div>
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <p class="text-sm text-neutral-500">Toplam katkın</p>
        <p class="text-2xl font-extrabold mt-1">{{ $tl($totalContribution) }}</p>
    </div>
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <p class="text-sm text-neutral-500">Üyelik tarihin</p>
        <p class="text-2xl font-extrabold mt-1">{{ $member?->joined_at?->format('d.m.Y') ?? '—' }}</p>
    </div>
</div>

{{-- Öğrenci doğrulama durumu --}}
@php $sv = $member?->studentVerification; @endphp
@if ($member && ($member->isStudent() || $sv))
    @if ($member->isStudentVerified())
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 mb-6 flex items-center gap-3">
            <span class="text-2xl">🎓</span>
            <div class="text-sm text-emerald-800">
                <p class="font-bold">Öğrenciliğin onaylı</p>
                <p>Öğrenci avantajların aktif@if ($sv?->valid_until) — {{ $sv->valid_until->format('d.m.Y') }} tarihine kadar geçerli@endif.</p>
            </div>
        </div>
    @else
        <a href="{{ route('uye.ogrenci-dogrulama') }}"
           class="block rounded-xl border border-amber-300 bg-amber-50 px-5 py-4 mb-6 hover:bg-amber-100 transition">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🎓</span>
                <div class="text-sm text-amber-900 flex-1">
                    <p class="font-bold">
                        @if ($sv && $sv->isPending()) Öğrenci belgen inceleniyor
                        @elseif ($sv && $sv->isExpired()) Öğrenci belgenin süresi doldu
                        @elseif ($sv) Öğrenci belgen reddedildi
                        @else Öğrenci avantajları için belgeni doğrula @endif
                    </p>
                    <p>
                        @if ($sv && $sv->isPending()) Yönetim onayından sonra avantajların aktifleşecek.
                        @else e-Devlet öğrenci belgeni yükle, yönetim onaylasın. →@endif
                    </p>
                </div>
            </div>
        </a>
    @endif
@endif

{{-- Hızlı işlemler --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('uye.aidat') }}" class="rounded-xl border border-neutral-200 bg-white p-5 hover:shadow">
        <p class="font-bold">💳 Aidat Öde</p>
        <p class="text-sm text-neutral-500 mt-1">Yıllık aidatını öde</p>
    </a>
    <a href="{{ route('uye.bagis') }}" class="rounded-xl border border-neutral-200 bg-white p-5 hover:shadow">
        <p class="font-bold">❤️ Bağış Yap</p>
        <p class="text-sm text-neutral-500 mt-1">Tribün fonuna destek ol</p>
    </a>
    <a href="{{ route('uye.profil') }}" class="rounded-xl border border-neutral-200 bg-white p-5 hover:shadow">
        <p class="font-bold">⚙️ Bilgilerim</p>
        <p class="text-sm text-neutral-500 mt-1">İletişim bilgilerini güncelle</p>
    </a>
</div>

{{-- Şeffaf kasa özeti --}}
<a href="{{ route('seffaf-kasa') }}" class="block rounded-xl border border-neutral-200 bg-white p-5 mb-6 hover:shadow">
    <div class="flex items-center justify-between mb-4">
        <p class="font-bold">📊 Şeffaf Kasa</p>
        <span class="text-sm text-[#9B0B22] font-semibold">Tümünü gör →</span>
    </div>
    <div class="grid grid-cols-3 gap-4 text-center">
        <div>
            <p class="text-xs text-neutral-500">Toplam Gelir</p>
            <p class="text-lg font-extrabold text-emerald-600">{{ $tl($kasa['gelir']) }}</p>
        </div>
        <div>
            <p class="text-xs text-neutral-500">Toplam Gider</p>
            <p class="text-lg font-extrabold text-rose-600">{{ $tl($kasa['gider']) }}</p>
        </div>
        <div>
            <p class="text-xs text-neutral-500">Bakiye</p>
            <p class="text-lg font-extrabold">{{ $tl($kasa['bakiye']) }}</p>
        </div>
    </div>
</a>

{{-- Yaklaşan etkinlikler / duyurular --}}
@if ($announcements->isNotEmpty())
    <h2 class="text-lg font-bold mb-3">Duyurular & Etkinlikler</h2>
    <div class="space-y-3 mb-6">
        @foreach ($announcements as $a)
            <a href="{{ route('haberler.show', $a) }}" class="flex items-center gap-4 rounded-xl border border-neutral-200 bg-white p-4 hover:shadow">
                <div class="flex-1 min-w-0">
                    <span class="text-[11px] font-bold uppercase px-2 py-0.5 rounded-full {{ $a->category === 'mac' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">{{ $a->categoryLabel() }}</span>
                    <p class="font-semibold mt-1 truncate">{{ $a->title }}</p>
                </div>
                <span class="text-sm text-neutral-400 whitespace-nowrap">{{ $a->published_at?->format('d.m.Y') }}</span>
            </a>
        @endforeach
    </div>
@endif

{{-- Üyelik avantajların --}}
@if ($features->isNotEmpty())
    <h2 class="text-lg font-bold mb-3">Üyelik Avantajların</h2>
    <div class="rounded-xl border border-neutral-200 bg-white p-5 mb-6">
        <p class="text-sm text-neutral-500 mb-4">
            <span class="font-semibold text-neutral-700">{{ $member?->category?->label() }}</span> üyeliğinle sahip olduğun haklar:
        </p>
        <ul class="space-y-3">
            @foreach ($features as $f)
                @php $val = $f->valueFor($featurePlanKey); @endphp
                <li class="flex items-start gap-3 text-sm">
                    @if ($val === 'no')
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        <span class="text-neutral-400 line-through">{{ $f->name }}</span>
                    @else
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span class="text-neutral-800">
                            {{ $f->name }}
                            @if ($val !== 'yes')
                                <span class="ml-1 font-semibold text-emerald-700">{{ $val }}</span>
                            @endif
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Daha üst kategoriye geçiş --}}
@if ($upgradePlans->isNotEmpty())
    <div class="rounded-xl border border-[#F7B500]/40 bg-amber-50 p-5 mb-6">
        <p class="font-bold text-neutral-800">Daha fazla avantaj ister misin?</p>
        <p class="text-sm text-neutral-600 mt-1">Üst kategoriye geçerek tribüne daha çok destek ol, yeni haklar kazan.</p>
        <div class="flex flex-wrap gap-3 mt-4">
            @foreach ($upgradePlans as $plan)
                <div class="rounded-lg border border-neutral-200 bg-white px-4 py-3">
                    <p class="font-bold">{{ $plan->name }}</p>
                    <p class="text-sm text-neutral-500">{{ $plan->price }} ₺ / yıl</p>
                </div>
            @endforeach
        </div>
        <a href="{{ route('home') }}#uyelik" class="inline-block mt-4 text-sm font-semibold text-[#9B0B22] hover:underline">Kategorileri karşılaştır →</a>
    </div>
@endif

{{-- Ödeme geçmişi --}}
<h2 class="text-lg font-bold mb-3">Ödeme Geçmişim</h2>
<div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-neutral-100 text-left">
                <th class="px-4 py-3">Tarih</th>
                <th class="px-4 py-3">Tür</th>
                <th class="px-4 py-3 text-right">Tutar</th>
                <th class="px-4 py-3">Durum</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $p)
                <tr class="border-t border-neutral-100">
                    <td class="px-4 py-3 text-neutral-600">{{ $p->created_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3 capitalize">{{ $p->purpose }}</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ $tl($p->amount) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $p->status === 'basarili' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $p->status }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-neutral-400">Henüz ödeme yok.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    (function () {
        var el = document.getElementById('member-qr');
        if (el && window.QRCode) {
            new QRCode(el, {
                text: el.dataset.value,
                width: 96,
                height: 96,
                colorDark: '#0b0b12',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
        }
    })();
</script>
@endpush
