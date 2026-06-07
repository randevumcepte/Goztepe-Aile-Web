@extends('layouts.admin')
@section('title', 'Öğrenci Doğrulama')

@section('content')
@php use App\Enums\StudentVerificationStatus; @endphp

{{-- Durum filtresi --}}
<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('admin.student-verifications.index') }}"
       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $activeStatus === '' ? 'bg-slate-800 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
        Tümü
    </a>
    @foreach ($statuses as $s)
        <a href="{{ route('admin.student-verifications.index', ['status' => $s->value]) }}"
           class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $activeStatus === $s->value ? 'bg-slate-800 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            {{ $s->label() }}
        </a>
    @endforeach
</div>

<div class="space-y-3">
@forelse ($verifications as $v)
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5"
         x-data="{ open: {{ $v->isPending() ? 'true' : 'false' }} }">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                    {{ strtoupper(substr($v->member?->user?->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-800">{{ $v->member?->user?->name }}</p>
                    <p class="text-xs text-slate-500">{{ $v->member?->user?->email }} · {{ $v->member?->member_no }}</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $v->status->badge() }}">{{ $v->status->label() }}</span>
        </div>

        {{-- Öğrenci bilgileri --}}
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-3 text-sm">
            <div>
                <p class="text-[11px] uppercase tracking-wider text-slate-400">Okul</p>
                <p class="font-medium text-slate-700">{{ $v->school ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-wider text-slate-400">Öğrenci No</p>
                <p class="font-mono text-slate-700">{{ $v->student_no }}</p>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-wider text-slate-400">T.C. No</p>
                <p class="font-mono text-slate-700">{{ $v->tc_no ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-wider text-slate-400">Barkod No</p>
                <p class="font-mono text-slate-700">{{ $v->document_barcode ?? '—' }}</p>
            </div>
        </div>

        @if ($v->valid_until || $v->rejection_reason || $v->reviewed_at)
            <div class="mt-3 text-xs text-slate-500 space-y-0.5">
                @if ($v->valid_until)<p>Geçerlilik: <strong>{{ $v->valid_until->format('d.m.Y') }}</strong></p>@endif
                @if ($v->rejection_reason)<p>Red sebebi: {{ $v->rejection_reason }}</p>@endif
                @if ($v->reviewed_at)<p>İnceleyen: {{ $v->reviewer?->name ?? '—' }} · {{ $v->reviewed_at->format('d.m.Y H:i') }}</p>@endif
            </div>
        @endif

        {{-- Aksiyonlar --}}
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.student-verifications.document', $v) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                📄 Belgeyi Gör
            </a>
            <a href="https://www.turkiye.gov.tr/belge-dogrulama" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 rounded-lg border border-blue-300 bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-100">
                e-Devlet'te Doğrula ↗
            </a>
            <button @click="open = !open" type="button"
                    class="ml-auto text-sm font-medium text-slate-500 hover:text-slate-800">Karar Ver ▾</button>
        </div>

        {{-- Karar paneli --}}
        <div x-show="open" x-cloak class="mt-4 border-t border-slate-100 pt-4" x-data="{ decision: 'onayla' }">
            <form method="POST" action="{{ route('admin.student-verifications.update', $v) }}" class="space-y-3">
                @csrf @method('PATCH')
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer rounded-lg border px-3 py-2 text-sm font-medium text-center"
                           :class="decision === 'onayla' ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="decision" value="onayla" x-model="decision" class="hidden"> ✓ Onayla
                    </label>
                    <label class="flex-1 cursor-pointer rounded-lg border px-3 py-2 text-sm font-medium text-center"
                           :class="decision === 'reddet' ? 'border-rose-400 bg-rose-50 text-rose-700' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="decision" value="reddet" x-model="decision" class="hidden"> ✕ Reddet
                    </label>
                </div>

                <div x-show="decision === 'onayla'">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Belge Geçerlilik Tarihi (dönem sonu)</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div x-show="decision === 'reddet'">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Red Sebebi</label>
                    <input type="text" name="rejection_reason" value="{{ old('rejection_reason') }}"
                           placeholder="Örn. Belge okunmuyor / barkod doğrulanamadı"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Kararı Kaydet</button>
            </form>
        </div>
    </div>
@empty
    <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center text-slate-400">
        Bu durumda doğrulama talebi yok.
    </div>
@endforelse
</div>

<div class="mt-4">{{ $verifications->links() }}</div>
@endsection
