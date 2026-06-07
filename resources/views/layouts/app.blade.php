<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Göztepe Tribünleri')</title>
    {{-- Build gerektirmez (sunucu uyumu). Üretimde CI ile derlenmiş CSS'e geçilebilir. --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-50 text-neutral-900 min-h-screen flex flex-col">
    <header class="bg-gradient-to-r from-[#D5102E] to-[#9B0B22] text-white">
        <div class="max-w-5xl mx-auto px-5 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('img/logo.svg') }}" alt="Göztepe Tribünleri" class="h-10 w-10">
                <span class="font-extrabold">Göztepe Tribünleri</span>
            </a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('seffaf-kasa') }}" class="hover:underline">Şeffaf Kasa</a>
                @auth
                    @if (auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="hover:underline">Yönetim</a>
                    @else
                        <a href="{{ route('uye.dashboard') }}" class="hover:underline">Panelim</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="bg-white/15 hover:bg-white/25 rounded px-3 py-1">Çıkış</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Giriş</a>
                    <a href="{{ route('register') }}" class="bg-[#F7B500] text-[#9B0B22] font-bold rounded px-3 py-1">Üye Ol</a>
                @endauth
            </nav>
        </div>
    </header>

    @if (session('status'))
        <div class="max-w-5xl mx-auto px-5 mt-4 w-full">
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">{{ session('status') }}</div>
        </div>
    @endif

    <main class="max-w-5xl mx-auto px-5 py-8 w-full flex-1">
        @yield('content')
    </main>

    <footer class="max-w-5xl mx-auto px-5 py-8 text-center text-xs text-neutral-400 w-full">
        Göztepe Tribünleri · Şeffaflık ve dayanışma
    </footer>

    @stack('scripts')
</body>
</html>
