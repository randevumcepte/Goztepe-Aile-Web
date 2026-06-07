@extends('layouts.app')
@section('title', 'Üye Ol — Göztepe Tribünleri')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-extrabold mb-6">Üye Ol</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4 bg-white p-6 rounded-xl border border-neutral-200">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Ad Soyad</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">E-posta</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Telefon</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Üyelik Kategorisi</label>
            <select name="category" class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                @foreach ($categories as $cat)
                    <option value="{{ $cat->value }}" @selected(old('category') === $cat->value)>{{ $cat->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Şifre</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Şifre (tekrar)</label>
            <input type="password" name="password_confirmation" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <label class="flex items-start gap-2 text-sm">
            <input type="checkbox" name="kvkk_consent" value="1" class="mt-1" required>
            <span>KVKK aydınlatma metnini okudum, kişisel verilerimin işlenmesini kabul ediyorum.</span>
        </label>
        <label class="flex items-start gap-2 text-sm">
            <input type="checkbox" name="commercial_consent" value="1" class="mt-1">
            <span>Kampanya ve duyurular için ticari ileti almak istiyorum (isteğe bağlı).</span>
        </label>
        <button class="w-full bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg py-2.5">Üyeliği Tamamla</button>
    </form>

    <p class="text-sm text-center mt-4 text-neutral-600">
        Zaten üye misin? <a href="{{ route('login') }}" class="text-[#D5102E] font-semibold">Giriş yap</a>
    </p>
</div>
@endsection
