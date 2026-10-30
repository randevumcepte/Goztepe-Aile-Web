@extends('layouts.admin')
@section('title', 'Taraftar Grupları')

@section('content')
<div class="mb-4 flex items-center justify-between gap-4">
    <p class="text-sm text-slate-500">Üyeler uygulamada "Hangi gruptasın?" ekranından grubunu seçer; grup kendi Taraftar Kartı'nda görünür. Üye isimlerini yalnız yönetim görür.</p>
    <a href="{{ route('admin.fan-groups.create') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Grup
    </a>
</div>

<div class="mb-4 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
    Grup seçmemiş (bağımsız) üye: <b>{{ $independent }}</b> · Gruba özel bildirim için
    <a href="{{ route('admin.notifications.create') }}" class="font-semibold text-brand-600 hover:underline">Bildirim Gönder</a>
    ekranında "Hedef Taraftar Grubu" seçin.
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($groups as $g)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-4 p-4">
                <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-amber-400 to-brand-600 text-2xl font-bold text-white">
                    @if ($g->logoUrl())<img src="{{ $g->logoUrl() }}" class="h-full w-full object-cover">@else{{ mb_substr($g->name, 0, 1) }}@endif
                </div>
                <div class="min-w-0">
                    <h3 class="truncate font-semibold text-slate-800">{{ $g->name }}</h3>
                    <p class="text-xs text-slate-500">{{ $g->tribune_area ?: 'Tribün yeri belirtilmedi' }}</p>
                    <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $g->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $g->is_active ? 'Aktif' : 'Pasif' }} · {{ $g->users_count }} üye</span>
                </div>
            </div>
            <div class="flex gap-2 border-t border-slate-100 px-4 py-3">
                <a href="{{ route('admin.fan-groups.show', $g) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Üyeler</a>
                <a href="{{ route('admin.fan-groups.edit', $g) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Düzenle</a>
                <form method="POST" action="{{ route('admin.fan-groups.destroy', $g) }}" onsubmit="return confirm('Grup silinsin mi? Üyeleri bağımsız olur.')">
                    @csrf @method('DELETE')
                    <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
            Henüz grup yok. "Yeni Grup" ile tribündeki grupları ekleyin; üyeler uygulamada bu listeden seçer.
        </div>
    @endforelse
</div>
@endsection
