@extends('layouts.admin')
@section('title', 'Avantaj Tablosu')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-500">Üyelik karşılaştırma tablosundaki satırlar (her plan için: var / yok / metin)</p>
    <a href="{{ route('admin.membership.features.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Avantaj
    </a>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3 font-semibold">Avantaj</th>
                    @foreach ($plans as $pl)<th class="px-3 py-3 text-center font-semibold">{{ $pl->name }}</th>@endforeach
                    <th class="px-3 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($features as $f)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $f->name }}</td>
                    @foreach ($plans as $pl)
                        @php $v = $f->valueFor($pl->key); @endphp
                        <td class="px-3 py-3 text-center">
                            @if ($v === 'yes')<span class="text-emerald-600">✓</span>
                            @elseif ($v === 'no')<span class="text-slate-300">—</span>
                            @else<span class="font-semibold text-amber-600">{{ $v }}</span>@endif
                        </td>
                    @endforeach
                    <td class="px-3 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.membership.features.edit', $f) }}" class="rounded-md px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Düzenle</a>
                            <form method="POST" action="{{ route('admin.membership.features.destroy', $f) }}" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ count($plans) + 2 }}" class="px-5 py-10 text-center text-slate-400">Henüz avantaj yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<a href="{{ route('admin.membership.plans.index') }}" class="mt-4 inline-block text-sm font-semibold text-brand-600 hover:underline">← Üyelik planlarına dön</a>
@endsection
