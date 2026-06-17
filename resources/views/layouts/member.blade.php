<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panelim') — Göztepe Tribünleri</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#fef2f3',100:'#fde6e8',200:'#fbd0d5',500:'#e11d36',600:'#D5102E',700:'#9B0B22',800:'#7d0a1c' },
                        gold: '#F7B500',
                    },
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body{font-family:'Inter',sans-serif}[x-cloak]{display:none!important}</style>
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased" x-data="{ open: false }">

@php
    $unread = auth()->user()?->notifications()->whereNull('read_at')->count() ?? 0;
    $nav = [
        ['group' => 'Genel', 'items' => [
            ['uye.dashboard', 'Panelim', null, '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 8.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
            ['uye.bildirimler', 'Bildirimler', $unread, '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />'],
        ]],
        ['group' => 'Kulüp', 'items' => [
            ['uye.fikstur', 'Fikstür', null, '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />'],
            ['uye.haberler', 'Haberler', null, '<path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />'],
            ['uye.seffaf-kasa', 'Şeffaf Kasa', null, '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />'],
        ]],
        ['group' => 'Üyelik', 'items' => [
            ['uye.aidat', 'Aidat Öde', null, '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3M3.75 5.25h16.5c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125H3.75c-.621 0-1.125-.504-1.125-1.125V6.375c0-.621.504-1.125 1.125-1.125z" />'],
            ['uye.bagis', 'Bağış Yap', null, '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />'],
            ['uye.profil', 'Bilgilerim', null, '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />'],
        ]],
    ];
@endphp

<div class="min-h-full">
    {{-- Mobil overlay --}}
    <div x-show="open" @click="open=false" x-cloak class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 transform bg-slate-900 text-slate-300 transition-transform lg:translate-x-0"
           :class="open ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex items-center gap-3 px-5 h-16 border-b border-white/10">
            <img src="{{ asset('img/logo.png') }}" alt="Göztepe Tribünleri" class="h-10 w-10 rounded-lg object-contain">
            <div class="leading-tight">
                <p class="text-white font-bold text-sm">Göztepe Tribünleri</p>
                <p class="text-[11px] text-slate-400">Üye Paneli</p>
            </div>
        </div>

        <nav class="px-3 py-4 space-y-6 overflow-y-auto" style="height: calc(100% - 4rem)">
            @foreach ($nav as $section)
                <div>
                    <p class="px-3 mb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $section['group'] }}</p>
                    <div class="space-y-1">
                        @foreach ($section['items'] as [$route, $label, $badge, $icon])
                            @php $active = request()->routeIs($route); @endphp
                            <a href="{{ route($route) }}"
                               class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                                      {{ $active ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                <svg class="h-5 w-5 {{ $active ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">{!! $icon !!}</svg>
                                <span class="flex-1">{{ $label }}</span>
                                @if ($badge)
                                    <span class="grid h-5 min-w-[1.25rem] place-items-center rounded-full bg-brand-600 px-1 text-[11px] font-bold text-white">{{ $badge }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </aside>

    {{-- İçerik --}}
    <div class="lg:pl-64">
        {{-- Topbar --}}
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur sm:px-6">
            <button @click="open=true" class="lg:hidden text-slate-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <h1 class="text-lg font-bold text-slate-900">@yield('title', 'Panelim')</h1>

            <div class="ml-auto flex items-center gap-3" x-data="{ menu:false }">
                <a href="{{ route('home') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-50">
                    Siteye Dön
                </a>
                <div class="relative">
                    <button @click="menu=!menu" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-100">
                        <div class="grid h-8 w-8 place-items-center rounded-full bg-brand-600 text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden text-left sm:block">
                            <p class="text-sm font-semibold leading-tight text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ auth()->user()->member?->member_no ?? 'Üye' }}</p>
                        </div>
                    </button>
                    <div x-show="menu" @click.outside="menu=false" x-cloak
                         class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
                        <a href="{{ route('uye.profil') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Bilgilerim</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="block w-full px-4 py-2 text-left text-sm text-brand-600 hover:bg-slate-50">Çıkış Yap</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="mx-4 mt-4 sm:mx-6 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mx-4 mt-4 sm:mx-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <ul class="list-inside list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <main class="p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
