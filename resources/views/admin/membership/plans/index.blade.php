@extends('layouts.admin')
@section('title', 'Üyelik Planları')

@section('content')
<p class="mb-4 text-sm text-slate-500">Ana sayfadaki üyelik kartları. Fiyat, açıklama ve kart maddelerini düzenle.</p>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($plans as $p)
        <div class="rounded-2xl border {{ $p->is_popular ? 'border-gold' : 'border-slate-200' }} bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900">{{ $p->name }}</h3>
                @if ($p->is_popular)<span class="rounded-full bg-gold px-2 py-0.5 text-[10px] font-bold uppercase text-brand-800">Popüler</span>@endif
            </div>
            <p class="mt-1 text-2xl font-extrabold text-brand-600">{{ $p->price }}₺ <span class="text-sm font-normal text-slate-400">/yıl</span></p>
            <p class="mt-1 text-xs text-slate-500">{{ $p->description }}</p>
            <ul class="mt-3 space-y-1 text-xs text-slate-600">
                @foreach (($p->card_features ?? []) as $f)<li>• {{ $f }}</li>@endforeach
            </ul>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs {{ $p->is_active ? 'text-emerald-600' : 'text-slate-400' }}">{{ $p->is_active ? 'Yayında' : 'Pasif' }}</span>
                <a href="{{ route('admin.membership.plans.edit', $p) }}" class="rounded-md bg-slate-800 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-900">Düzenle</a>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-6">
    <a href="{{ route('admin.membership.features.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Avantaj karşılaştırma tablosunu düzenle →</a>
</div>
@endsection
