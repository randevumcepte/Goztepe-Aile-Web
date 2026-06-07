@extends('layouts.admin')
@section('title', 'Üyeler')

@section('content')
<form method="GET" class="mb-4 flex gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="Ad, e-posta veya üye no ara..."
           class="rounded-lg border border-neutral-300 px-3 py-2 text-sm w-72">
    <button class="bg-neutral-800 text-white rounded-lg px-4 py-2 text-sm">Ara</button>
</form>

<div class="bg-white rounded-xl border border-neutral-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead><tr class="bg-neutral-100 text-left">
            <th class="px-4 py-2">Üye No</th><th class="px-4 py-2">Ad</th>
            <th class="px-4 py-2">E-posta</th><th class="px-4 py-2">Kategori / Durum</th>
            <th class="px-4 py-2">İşlem</th>
        </tr></thead>
        <tbody>
        @forelse ($members as $m)
            <tr class="border-t border-neutral-100">
                <td class="px-4 py-2 font-mono text-xs">{{ $m->member_no }}</td>
                <td class="px-4 py-2">{{ $m->user?->name }}</td>
                <td class="px-4 py-2 text-neutral-500">{{ $m->user?->email }}</td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('admin.members.update', $m) }}" class="flex gap-2 items-center">
                        @csrf @method('PATCH')
                        <select name="category" class="rounded border border-neutral-300 px-2 py-1 text-xs">
                            @foreach ($categories as $c)
                                <option value="{{ $c->value }}" @selected($m->category === $c)>{{ $c->label() }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="rounded border border-neutral-300 px-2 py-1 text-xs">
                            @foreach (['beklemede','aktif','pasif'] as $s)
                                <option value="{{ $s }}" @selected($m->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button class="bg-neutral-800 text-white rounded px-3 py-1 text-xs">Kaydet</button>
                    </form>
                </td>
                <td class="px-4 py-2">
                    @if ($m->hasVote())<span class="text-xs text-emerald-700">Oy hakkı ✓</span>@endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-neutral-400">Üye bulunamadı.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $members->links() }}</div>
@endsection
