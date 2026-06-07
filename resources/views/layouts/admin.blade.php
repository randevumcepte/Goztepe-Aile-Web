<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Yönetim') — Göztepe Tribünleri</title>
    {{-- Build gerektirmez (sunucu uyumu). Üretimde CI ile derlenmiş CSS'e geçilebilir. --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-100 text-neutral-900">
<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="w-60 bg-neutral-900 text-neutral-200 flex-shrink-0 hidden md:block">
        <div class="px-5 py-5 flex items-center gap-3 border-b border-neutral-800">
            <div class="w-9 h-9 rounded-full bg-[#F7B500] text-[#9B0B22] font-black grid place-items-center">G</div>
            <span class="font-bold text-white text-sm">Yönetim</span>
        </div>
        @php
            $nav = [
                ['admin.dashboard', 'Özet', '📊'],
                ['admin.transactions.index', 'Gelir / Gider', '💰'],
                ['admin.members.index', 'Üyeler', '👥'],
                ['admin.notifications.create', 'Bildirim Gönder', '🔔'],
                ['admin.campaigns.index', 'Kampanyalar', '📣'],
            ];
        @endphp
        <nav class="py-3 text-sm">
            @foreach ($nav as [$route, $label, $icon])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 px-5 py-2.5 hover:bg-neutral-800 {{ request()->routeIs($route) ? 'bg-neutral-800 text-white border-l-4 border-[#D5102E]' : '' }}">
                    <span>{{ $icon }}</span> {{ $label }}
                </a>
            @endforeach
        </nav>
    </aside>

    {{-- İçerik --}}
    <div class="flex-1 flex flex-col">
        <header class="bg-white border-b border-neutral-200 px-6 py-3 flex items-center justify-between">
            <h1 class="font-bold">@yield('title', 'Yönetim')</h1>
            <div class="flex items-center gap-3 text-sm">
                <span class="text-neutral-500">{{ auth()->user()->name }} · {{ auth()->user()->role->label() }}</span>
                <a href="{{ route('seffaf-kasa') }}" class="text-neutral-500 hover:underline">Siteyi gör</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="bg-neutral-100 rounded px-3 py-1">Çıkış</button>
                </form>
            </div>
        </header>

        @if (session('status'))
            <div class="mx-6 mt-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mx-6 mt-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <main class="p-6 flex-1">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
