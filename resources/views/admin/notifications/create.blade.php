@extends('layouts.admin')
@section('title', 'Bildirim Gönder')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.notifications.send') }}"
          class="bg-white rounded-xl border border-neutral-200 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Başlık</label>
            <input name="title" value="{{ old('title') }}" required
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Mesaj</label>
            <textarea name="body" rows="3" class="w-full rounded-lg border border-neutral-300 px-3 py-2">{{ old('body') }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tür</label>
                <select name="type" class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                    <option value="islemsel">İşlemsel (izinsiz gönderilebilir)</option>
                    <option value="ticari">Ticari (yalnız rıza verenlere)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Hedef Kategori</label>
                <select name="segment_kategori" class="w-full rounded-lg border border-neutral-300 px-3 py-2">
                    <option value="">Tüm üyeler</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->value }}">{{ $c->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Kanallar</label>
            <div class="flex gap-4 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="channels[]" value="in_app" checked> Uygulama içi</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="channels[]" value="push" checked> Push</label>
            </div>
        </div>
        <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs px-3 py-2">
            ⚠️ Ticari (reklam/kampanya) bildirimler yalnız "ticari ileti rızası" veren üyelere gider (İYS/KVKK).
        </div>
        <button class="bg-[#D5102E] hover:bg-[#9B0B22] text-white font-bold rounded-lg px-6 py-2.5">Gönder</button>
    </form>
</div>
@endsection
