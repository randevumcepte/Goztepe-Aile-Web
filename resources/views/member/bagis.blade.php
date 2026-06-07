@extends('layouts.app')
@section('title', 'Bağış Yap')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-extrabold mb-6">Bağış Yap</h1>
    <form method="POST" action="{{ route('uye.odeme.start') }}" class="bg-white p-6 rounded-xl border border-neutral-200 space-y-4">
        @csrf
        <input type="hidden" name="purpose" value="bagis">
        <div>
            <label class="block text-sm font-medium mb-1">Bağış Tutarı (₺)</label>
            <input type="number" name="amount" min="1" step="1" value="100" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-lg font-bold">
        </div>
        <div class="flex gap-2">
            @foreach ([50, 100, 250, 500] as $preset)
                <button type="button" onclick="document.querySelector('[name=amount]').value={{ $preset }}"
                        class="flex-1 border border-neutral-300 rounded-lg py-2 text-sm hover:bg-neutral-50">{{ $preset }} ₺</button>
            @endforeach
        </div>
        <button class="w-full bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg py-2.5">Bağış Yap</button>
        <p class="text-xs text-neutral-400 text-center">Bağışın dernek fonuna işlenir ve şeffaf kasada görünür.</p>
    </form>
</div>
@endsection
