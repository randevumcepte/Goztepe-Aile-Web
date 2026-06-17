@extends('layouts.member')
@section('title', 'Bilgilerim')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-extrabold mb-1">Bilgilerim</h1>
    <p class="text-neutral-600 text-sm mb-6">İletişim bilgilerini güncel tut — bildirimleri ve duyuruları kaçırma.</p>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('uye.profil.update') }}"
          class="space-y-4 bg-white p-6 rounded-xl border border-neutral-200">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Ad Soyad</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">E-posta</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Telefon <span class="text-neutral-400 font-normal">(isteğe bağlı)</span></label>
            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                   placeholder="05xx xxx xx xx"
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>

        <button class="w-full bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg py-2.5">
            Kaydet
        </button>
    </form>
</div>
@endsection
