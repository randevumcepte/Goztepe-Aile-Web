{{-- Reklam / kampanya pop-up'ı (in-app modal). $campaign: App\Models\InAppMessage --}}
<div x-data="{ show: true }" x-show="show" x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4"
     style="display:none">
    {{-- Karartma --}}
    <div class="absolute inset-0 bg-slate-900/70" @click="show = false"></div>

    {{-- Kart --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">

        {{-- Kapat --}}
        <button type="button" @click="show = false"
                class="absolute right-3 top-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        {{-- Görsel --}}
        @if ($campaign->media_path)
            <img src="{{ asset('uploads/'.$campaign->media_path) }}" alt="{{ $campaign->title }}"
                 class="w-full object-cover">
        @endif

        <div class="p-6 text-center">
            <h3 class="text-xl font-extrabold text-slate-900">{{ $campaign->title }}</h3>
            @if ($campaign->content)
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $campaign->content }}</p>
            @endif

            <a href="{{ route('uye.kampanya.git', $campaign) }}"
               class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#D5102E] px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-[#9B0B22]">
                {{ $campaign->cta_label ?: 'Bağış Yap' }}
            </a>
        </div>
    </div>
</div>
