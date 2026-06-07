@extends('layouts.admin')
@section('title', 'Kampanyalar')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-500">In-app pop-up / reklam kampanyaları</p>
    <a href="{{ route('admin.campaigns.create') }}"
       class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Kampanya
    </a>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3 font-semibold">Başlık</th>
                    <th class="px-5 py-3 font-semibold">Tip</th>
                    <th class="px-5 py-3 font-semibold">Ticari</th>
                    <th class="px-5 py-3 font-semibold">Gösterim</th>
                    <th class="px-5 py-3 font-semibold">Durum</th>
                    <th class="px-5 py-3 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($campaigns as $c)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $c->title }}</td>
                    <td class="px-5 py-3"><span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $c->type }}</span></td>
                    <td class="px-5 py-3 text-slate-500">{{ $c->is_commercial ? 'Evet' : 'Hayır' }}</td>
                    <td class="px-5 py-3 font-semibold text-slate-700">{{ $c->impressions_count }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $c->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $c->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ $c->is_active ? 'Aktif' : 'Pasif' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <form method="POST" action="{{ route('admin.campaigns.toggle', $c) }}">
                            @csrf @method('PATCH')
                            <button class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                {{ $c->is_active ? 'Durdur' : 'Başlat' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Henüz kampanya yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $campaigns->links() }}</div>
@endsection
