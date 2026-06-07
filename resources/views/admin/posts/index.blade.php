@extends('layouts.admin')
@section('title', 'Haberler')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-slate-500">Site haberlerini yönet</p>
    <a href="{{ route('admin.posts.create') }}"
       class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Yeni Haber
    </a>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3 font-semibold">Görsel</th>
                    <th class="px-5 py-3 font-semibold">Başlık</th>
                    <th class="px-5 py-3 font-semibold">Kategori</th>
                    <th class="px-5 py-3 font-semibold">Durum</th>
                    <th class="px-5 py-3 font-semibold">Tarih</th>
                    <th class="px-5 py-3 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($posts as $p)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">
                        @if ($p->coverUrl())
                            <img src="{{ $p->coverUrl() }}" class="h-10 w-16 rounded object-cover">
                        @else
                            <div class="grid h-10 w-16 place-items-center rounded bg-slate-100 text-[10px] text-slate-400">yok</div>
                        @endif
                    </td>
                    <td class="px-5 py-3 font-medium text-slate-800">{{ \Illuminate\Support\Str::limit($p->title, 50) }}</td>
                    <td class="px-5 py-3"><span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600">{{ $p->categoryLabel() }}</span></td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $p->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ $p->is_published ? 'Yayında' : 'Taslak' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-500">{{ $p->published_at?->format('d.m.Y') ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.posts.edit', $p) }}" class="rounded-md px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Düzenle</a>
                            <form method="POST" action="{{ route('admin.posts.destroy', $p) }}" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="rounded-md px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Henüz haber yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $posts->links() }}</div>
@endsection
