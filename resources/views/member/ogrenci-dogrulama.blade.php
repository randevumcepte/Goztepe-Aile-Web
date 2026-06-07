@extends('layouts.app')
@section('title', 'Öğrenci Doğrulama — Göztepe Tribünleri')

@section('content')
@php use App\Enums\StudentVerificationStatus; @endphp

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('uye.dashboard') }}" class="text-sm text-neutral-500 hover:underline">← Panelim</a>
    </div>
    <h1 class="text-2xl font-extrabold mb-1">Öğrenci Doğrulama</h1>
    <p class="text-neutral-600 text-sm mb-6">Öğrenci üyelik avantajlarından yararlanmak için öğrenci belgeni doğrula.</p>

    {{-- Mevcut durum --}}
    @if ($verification)
        <div class="rounded-xl border border-neutral-200 bg-white p-5 mb-6">
            <div class="flex items-center justify-between">
                <span class="font-semibold">Doğrulama Durumu</span>
                <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $verification->status->badge() }}">
                    {{ $verification->status->label() }}
                </span>
            </div>

            @if ($verification->status === StudentVerificationStatus::Onayli && $verification->isValid())
                <p class="text-sm text-emerald-700 mt-3">
                    ✓ Öğrenciliğin onaylandı. Avantajların aktif.
                    @if ($verification->valid_until)
                        <br><span class="text-neutral-500">Geçerlilik: {{ $verification->valid_until->format('d.m.Y') }} tarihine kadar.</span>
                    @endif
                </p>
            @elseif ($verification->isExpired())
                <p class="text-sm text-amber-700 mt-3">
                    Belgenin geçerlilik süresi doldu ({{ $verification->valid_until->format('d.m.Y') }}). Lütfen güncel öğrenci belgeni tekrar yükle.
                </p>
            @elseif ($verification->status === StudentVerificationStatus::Reddedildi)
                <p class="text-sm text-rose-700 mt-3">
                    Belgen reddedildi. @if ($verification->rejection_reason) <strong>Sebep:</strong> {{ $verification->rejection_reason }} @endif
                    <br>Aşağıdan doğru belgeyi tekrar yükleyebilirsin.
                </p>
            @else
                <p class="text-sm text-amber-700 mt-3">Belgen yönetim incelemesinde. Sonuç panelinde görünecek.</p>
            @endif
        </div>
    @endif

    {{-- e-Devlet yönlendirme --}}
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 mb-6 text-sm text-blue-900">
        <p class="font-semibold mb-1">📄 Öğrenci belgeni nereden alırsın?</p>
        <p>e-Devlet'ten <strong>barkodlu öğrenci belgesi</strong> al, PDF olarak indir ve aşağıya yükle.</p>
        <a href="https://www.turkiye.gov.tr/yok-ogrenci-belgesi-sorgulama" target="_blank" rel="noopener"
           class="inline-block mt-2 font-semibold underline">e-Devlet Öğrenci Belgesi →</a>
    </div>

    {{-- Form: onaylı-geçerli değilse göster --}}
    @if (! ($verification && $verification->isValid()))
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('uye.ogrenci-dogrulama.store') }}" enctype="multipart/form-data"
              class="space-y-4 bg-white p-6 rounded-xl border border-neutral-200">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Okul / Üniversite</label>
                <input type="text" name="school" value="{{ old('school', $verification?->school) }}" required
                       placeholder="Örn. Dokuz Eylül Üniversitesi"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Öğrenci Numarası</label>
                <input type="text" name="student_no" value="{{ old('student_no', $verification?->student_no) }}" required
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                <p class="text-xs text-neutral-500 mt-1">Her öğrenci numarası sistemde yalnızca bir kez kullanılabilir.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">T.C. Kimlik No</label>
                <input type="text" name="tc_no" value="{{ old('tc_no', $verification?->tc_no) }}" required
                       maxlength="11" inputmode="numeric"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                <p class="text-xs text-neutral-500 mt-1">Belgenin e-Devlet'ten doğrulanması için gereklidir.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Belge Barkod No <span class="text-neutral-400 font-normal">(isteğe bağlı)</span></label>
                <input type="text" name="document_barcode" value="{{ old('document_barcode', $verification?->document_barcode) }}"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                <p class="text-xs text-neutral-500 mt-1">Belgenin üzerindeki barkod/karekod numarası — doğrulamayı hızlandırır.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Öğrenci Belgesi (PDF / JPG / PNG, en fazla 4 MB)</label>
                <input type="file" name="document" required accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#D5102E] file:text-white file:px-4 file:py-2 file:font-semibold">
            </div>

            <p class="text-xs text-neutral-500">
                KVKK: Yüklediğin belge yalnızca öğrencilik doğrulaması amacıyla, yetkili yönetim tarafından görüntülenir ve gizli tutulur.
            </p>

            <button class="w-full bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg py-2.5">
                {{ $verification ? 'Belgeyi Güncelle' : 'Belgeyi Gönder' }}
            </button>
        </form>
    @endif
</div>
@endsection
