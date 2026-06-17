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
            <p class="text-xs text-neutral-500 mt-1">
                🎓 Öğrenci üyeliğinde, kayıttan sonra panelinden öğrenci belgeni yükleyip doğrulatman gerekir.
            </p>
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
        {{-- KVKK açık rıza --}}
        <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-3 space-y-2">
            <details class="text-sm text-neutral-600">
                <summary class="cursor-pointer font-medium text-neutral-800">KVKK Aydınlatma ve Açık Rıza Metni</summary>
                <div class="mt-2 space-y-2 text-xs leading-relaxed">
                    <p>Göztepe Tribünleri olarak; üyelik kaydınız sırasında verdiğiniz ad-soyad, e-posta, telefon
                    ve üyelik bilgileriniz, 6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında, üyelik
                    işlemlerinizin yürütülmesi, aidat ve etkinlik süreçlerinin yönetilmesi ve sizinle iletişim
                    kurulması amacıyla işlenir.</p>
                    <p>Verileriniz, yasal yükümlülükler ve hizmetin gereği dışında üçüncü kişilerle paylaşılmaz;
                    mevzuatın öngördüğü süre boyunca güvenli şekilde saklanır. KVKK’nın 11. maddesi uyarınca
                    verilerinize erişme, düzeltilmesini veya silinmesini isteme haklarına sahipsiniz.</p>
                    <p>Bu kutucuğu işaretleyerek, aydınlatma metnini okuduğunuzu ve kişisel verilerinizin yukarıdaki
                    kapsamda işlenmesine açık rıza verdiğinizi beyan etmiş olursunuz.</p>
                </div>
            </details>
            <label class="flex items-start gap-2 text-sm">
                <input type="checkbox" name="kvkk_consent" value="1" class="mt-1" required>
                <span>KVKK aydınlatma metnini okudum, kişisel verilerimin işlenmesine <strong>açık rıza</strong> veriyorum. <span class="text-red-600">(zorunlu)</span></span>
            </label>
        </div>

        {{-- Ticari ileti (İYS) açık rıza --}}
        <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-3">
            <label class="flex items-start gap-2 text-sm">
                <input type="checkbox" name="commercial_consent" value="1" class="mt-1">
                <span>Göztepe Tribünleri’nin <strong>reklam, kampanya ve duyuruları</strong> kapsamında tarafıma
                <strong>bildirim, telefonla arama ve SMS</strong> gönderilmesini kabul ediyorum.
                <span class="text-neutral-500">(İYS — isteğe bağlı, dilediğin zaman panelinden kapatabilirsin)</span></span>
            </label>
        </div>
        <button class="w-full bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg py-2.5">Üyeliği Tamamla</button>
    </form>

    <p class="text-sm text-center mt-4 text-neutral-600">
        Zaten üye misin? <a href="{{ route('login') }}" class="text-[#D5102E] font-semibold">Giriş yap</a>
    </p>
</div>
@endsection
