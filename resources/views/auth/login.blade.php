@extends('layouts.app')
@section('title', 'Giriş — Göztepe Tribünleri')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-extrabold mb-6">Giriş Yap</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4 bg-white p-6 rounded-xl border border-neutral-200">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">E-posta</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Şifre</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember"> Beni hatırla
        </label>
        <button class="w-full bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg py-2.5">Giriş Yap</button>
    </form>

    <p class="text-sm text-center mt-4 text-neutral-600">
        Hesabın yok mu? <a href="{{ route('register') }}" class="text-[#D5102E] font-semibold">Üye ol</a>
    </p>
</div>
@endsection
