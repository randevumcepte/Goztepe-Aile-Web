<!DOCTYPE html>
<html lang="tr" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Göztepe Tribünleri')</title>
    <meta name="description" content="@yield('meta', 'Göztepe Tribünleri — İzmir\'in gür sesi. Haberler, tribün kültürü, üyelik ve şeffaf kasa.')">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: {
                    brand: { 500:'#e11d36', 600:'#D5102E', 700:'#9B0B22', 800:'#7d0a1c', 900:'#5e0815' },
                    gold: { DEFAULT:'#F7B500', 400:'#FFC72C' },
                    ink: '#0b0b12',
                },
                fontFamily: {
                    sans: ['Inter','ui-sans-serif','system-ui','sans-serif'],
                    display: ['Oswald','Inter','sans-serif'],
                },
            }}
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif}
        h1,h2,h3,.font-display{font-family:'Oswald',sans-serif;letter-spacing:.02em}
        [x-cloak]{display:none!important}
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-ink text-slate-100 antialiased" x-data="{ open:false }">

@php
    $menu = [
        ['seffaf-kasa', 'Ana Sayfa', request()->routeIs('home')],
        ['haberler', 'Haberler', request()->routeIs('haberler.*')],
        ['hakkimizda', 'Hakkımızda', request()->routeIs('hakkimizda')],
        ['seffaf-kasa', 'Şeffaf Kasa', request()->routeIs('seffaf-kasa')],
        ['iletisim', 'İletişim', request()->routeIs('iletisim')],
    ];
    $nav = [
        ['home', 'Ana Sayfa'],
        ['haberler.index', 'Haberler'],
        ['sanli-tarihimiz', 'Şanlı Tarihimiz'],
        ['uyelik.avantajlar', 'Üyelik'],
        ['hakkimizda', 'Hakkımızda'],
        ['seffaf-kasa', 'Şeffaf Kasa'],
        ['iletisim', 'İletişim'],
    ];
    $cfg = \App\Models\Setting::map();
    $siteName = $cfg['site_name'] ?? 'Göztepe Tribünleri';
@endphp

{{-- Üst bar --}}
<div class="hidden bg-gold text-ink sm:block">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-1.5 text-xs font-semibold">
        <div class="flex items-center gap-4">
            <span>{{ \Carbon\Carbon::now()->locale('tr')->translatedFormat('l, d F Y') }}</span>
            <span class="hidden md:inline">{{ $cfg['topbar_text'] ?? 'İzmir\'in Gür Sesi · 1925' }}</span>
            @if (!empty($cfg['phone']))<span class="hidden md:inline">{{ $cfg['phone'] }}</span>@endif
        </div>
        <div class="flex items-center gap-3">
            @if (!empty($cfg['instagram_url']))<a href="{{ $cfg['instagram_url'] }}" target="_blank" class="hover:opacity-70">Instagram</a>@endif
            @if (!empty($cfg['x_url']))<a href="{{ $cfg['x_url'] }}" target="_blank" class="hover:opacity-70">X</a>@endif
            @if (!empty($cfg['youtube_url']))<a href="{{ $cfg['youtube_url'] }}" target="_blank" class="hover:opacity-70">YouTube</a>@endif
        </div>
    </div>
</div>

{{-- Header / Nav --}}
<header class="sticky top-0 z-40 border-b border-white/10 bg-brand-700/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/logo.svg') }}" alt="Göztepe Tribünleri" class="h-12 w-12 drop-shadow">
            <div class="leading-tight">
                <p class="font-display text-lg font-bold uppercase text-white">Göztepe Tribünleri</p>
                <p class="text-[11px] text-gold-400">Taraftar Derneği</p>
            </div>
        </a>

        <nav class="hidden items-center gap-1 lg:flex">
            @foreach ($nav as [$route, $label])
                @php $active = request()->routeIs($route) || ($route==='haberler.index' && request()->routeIs('haberler.*')); @endphp
                <a href="{{ route($route) }}"
                   class="rounded-md px-3 py-2 text-sm font-semibold uppercase tracking-wide transition
                          {{ $active ? 'text-gold' : 'text-white/90 hover:text-gold' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            @auth
                <a href="{{ auth()->user()->isStaff() ? route('admin.dashboard') : route('uye.dashboard') }}"
                   class="hidden rounded-lg bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/20 sm:block">Panelim</a>
            @else
                <a href="{{ route('login') }}" class="hidden px-3 py-2 text-sm font-semibold text-white/90 hover:text-gold sm:block">Giriş</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-gold px-4 py-2 text-sm font-bold uppercase text-brand-800 hover:bg-gold-400">Üye Ol</a>
            @endauth
            <button @click="open=!open" class="lg:hidden text-white">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
        </div>
    </div>

    {{-- Mobil menü --}}
    <div x-show="open" x-cloak class="border-t border-white/10 bg-brand-800 lg:hidden">
        <nav class="mx-auto max-w-7xl px-4 py-2">
            @foreach ($nav as [$route, $label])
                <a href="{{ route($route) }}" class="block rounded-md px-3 py-2.5 text-sm font-semibold uppercase text-white/90 hover:bg-white/5 hover:text-gold">{{ $label }}</a>
            @endforeach
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

{{-- Footer --}}
<footer class="border-t border-white/10 bg-brand-900">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 py-12 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <div class="flex items-center gap-3">
                <img src="{{ asset('img/logo.svg') }}" alt="Göztepe Tribünleri" class="h-11 w-11">
                <span class="font-display text-lg font-bold uppercase text-white">Göztepe Tribünleri</span>
            </div>
            <p class="mt-3 text-sm text-white/60">İzmir'in gür sesi. Taraftarın gücüyle, şeffaf ve dayanışmacı bir camia.</p>
        </div>
        <div>
            <h4 class="font-display text-sm font-bold uppercase text-gold">Keşfet</h4>
            <ul class="mt-3 space-y-2 text-sm text-white/70">
                <li><a href="{{ route('haberler.index') }}" class="hover:text-gold">Haberler</a></li>
                <li><a href="{{ route('sanli-tarihimiz') }}" class="hover:text-gold">Şanlı Tarihimiz</a></li>
                <li><a href="{{ route('hakkimizda') }}" class="hover:text-gold">Hakkımızda</a></li>
                <li><a href="{{ route('seffaf-kasa') }}" class="hover:text-gold">Şeffaf Kasa</a></li>
                <li><a href="{{ route('iletisim') }}" class="hover:text-gold">İletişim</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-display text-sm font-bold uppercase text-gold">Topluluk</h4>
            <ul class="mt-3 space-y-2 text-sm text-white/70">
                <li><a href="{{ route('register') }}" class="hover:text-gold">Üye Ol</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-gold">Giriş Yap</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-display text-sm font-bold uppercase text-gold">İletişim</h4>
            <ul class="mt-3 space-y-2 text-sm text-white/70">
                <li>{{ $cfg['address'] ?? 'İzmir, Türkiye' }}</li>
                <li>{{ $cfg['email'] ?? 'info@goztepetribunleri.com' }}</li>
                @if (!empty($cfg['phone']))<li>{{ $cfg['phone'] }}</li>@endif
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 py-4 text-center text-xs text-white/40">
        © {{ date('Y') }} Göztepe Tribünleri Taraftar Derneği · Tüm hakları saklıdır.
    </div>
</footer>
</body>
</html>
