@extends('layouts.admin')
@section('title', 'Kampanyalar')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h2 class="font-bold">In-App Kampanyalar / Pop-up</h2>
    <a href="{{ route('admin.campaigns.create') }}" class="bg-[#D5102E] text-white text-sm rounded-lg px-4 py-2">+ Yeni Kampanya</a>
</div>

<div class="bg-white rounded-xl border border-neutral-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="bg-neutral-100 text-left">
            <th class="px-4 py-2">Başlık</th><th class="px-4 py-2">Tip</th>
            <th class="px-4 py-2">Ticari</th><th class="px-4 py-2">Gösterim</th>
            <th class="px-4 py-2">Durum</th><th class="px-4 py-2"></th>
        </tr></thead>
        <tbody>
        @forelse ($campaigns as $c)
            <tr class="border-t border-neutral-100">
                <td class="px-4 py-2 font-medium">{{ $c->title }}</td>
                <td class="px-4 py-2">{{ $c->type }}</td>
                <td class="px-4 py-2">{{ $c->is_commercial ? 'Evet' : 'Hayır' }}</td>
                <td class="px-4 py-2">{{ $c->impressions_count }}</td>
                <td class="px-4 py-2">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $c->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-neutral-200 text-neutral-600' }}">
                        {{ $c->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </td>
                <td class="px-4 py-2 text-right">
                    <form method="POST" action="{{ route('admin.campaigns.toggle', $c) }}">
                        @csrf @method('PATCH')
                        <button class="text-xs text-neutral-700">{{ $c->is_active ? 'Durdur' : 'Başlat' }}</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-6 text-center text-neutral-400">Kampanya yok.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $campaigns->links() }}</div>
@endsection
