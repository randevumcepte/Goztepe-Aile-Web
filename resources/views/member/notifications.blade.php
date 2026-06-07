@extends('layouts.app')
@section('title', 'Bildirimlerim')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-extrabold">Bildirimler</h1>
    <a href="{{ route('uye.dashboard') }}" class="text-sm text-neutral-500">← Panele dön</a>
</div>

<div class="space-y-3">
    @forelse ($notifications as $n)
        <div class="rounded-xl border {{ $n->read_at ? 'border-neutral-200 bg-white' : 'border-[#F7B500] bg-amber-50' }} p-4">
            <div class="flex justify-between">
                <p class="font-semibold">{{ $n->title }}</p>
                <span class="text-xs text-neutral-400">{{ $n->created_at->diffForHumans() }}</span>
            </div>
            @if ($n->body)<p class="text-sm text-neutral-600 mt-1">{{ $n->body }}</p>@endif
        </div>
    @empty
        <p class="text-center text-neutral-400 py-12">Henüz bildirim yok.</p>
    @endforelse
</div>

<div class="mt-6">{{ $notifications->links() }}</div>
@endsection
