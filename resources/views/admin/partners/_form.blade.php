@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $hint = 'mt-1 text-xs text-slate-400';
    $partner = $partner ?? null;
    $file = 'mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100';
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @if ($partner && ($partner->applicant_name || $partner->applicant_phone))
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <p class="font-semibold">Uygulamadan gelen başvuru</p>
            <p class="mt-1">Başvuran: <strong>{{ $partner->applicant_name ?: '—' }}</strong>
                @if ($partner->applicant_phone) · <a href="tel:{{ $partner->applicant_phone }}" class="underline">{{ $partner->applicant_phone }}</a>@endif
                · {{ $partner->created_at?->format('d.m.Y H:i') }}</p>
            @if ($partner->status === 'beklemede')
                <p class="mt-1 text-xs">Onaylamak için durumu "Aktif" yapın ve işletmeye bir okutma PIN'i belirleyin.</p>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <label class="{{ $label }}">Durum</label>
            <select name="status" class="{{ $input }}">
                @foreach (['beklemede' => 'Beklemede (başvuru)', 'aktif' => 'Aktif', 'pasif' => 'Pasif'] as $k => $v)
                    <option value="{{ $k }}" @selected(old('status', $partner?->status ?? 'aktif') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="{{ $label }}">İşletme Adı</label>
            <input name="name" value="{{ old('name', $partner?->name) }}" required maxlength="120" placeholder="Tribün Kahvecisi" class="{{ $input }}">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Kategori</label>
            <select name="category" class="{{ $input }}">
                @foreach (\App\Models\Partner::CATEGORIES as $k => $v)
                    <option value="{{ $k }}" @selected(old('category', $partner?->category ?? 'kafe') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">İndirim Metni</label>
            <input name="discount_text" value="{{ old('discount_text', $partner?->discount_text) }}" maxlength="120" placeholder="%15 indirim" class="{{ $input }}">
        </div>
    </div>

    <div>
        <label class="{{ $label }}">Kısa Açıklama</label>
        <textarea name="description" rows="2" maxlength="500" class="{{ $input }}" placeholder="Stadın dibinde, maç öncesi buluşma noktası…">{{ old('description', $partner?->description) }}</textarea>
    </div>

    <div>
        <label class="{{ $label }}">Koşullar</label>
        <textarea name="conditions" rows="3" maxlength="2000" class="{{ $input }}" placeholder="Kampanyalarla birleşmez. Alkollü içeceklerde geçerli değildir.">{{ old('conditions', $partner?->conditions) }}</textarea>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="sm:col-span-2">
            <label class="{{ $label }}">Adres</label>
            <input name="address" value="{{ old('address', $partner?->address) }}" maxlength="255" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Semt / İlçe</label>
            <input name="district" value="{{ old('district', $partner?->district) }}" maxlength="80" placeholder="Göztepe / Konak" class="{{ $input }}">
        </div>
    </div>

    <div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $label }}">Enlem (lat)</label>
                <input name="lat" type="number" step="any" value="{{ old('lat', $partner?->lat) }}" placeholder="38.3965" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">Boylam (lng)</label>
                <input name="lng" type="number" step="any" value="{{ old('lng', $partner?->lng) }}" placeholder="27.0779" class="{{ $input }}">
            </div>
        </div>
        <p class="{{ $hint }}">Google Haritalar'da konuma sağ tıklayıp koordinatı kopyalayın (ilk sayı enlem, ikincisi boylam). Haritada gösterilmesi için gereklidir.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Telefon</label>
            <input name="phone" value="{{ old('phone', $partner?->phone) }}" maxlength="40" placeholder="0232 000 00 00" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Çalışma Saatleri</label>
            <input name="hours" value="{{ old('hours', $partner?->hours) }}" maxlength="120" placeholder="Her gün 08:00–23:00" class="{{ $input }}">
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="mb-3 text-sm font-semibold text-slate-800">Damga Kartı & Kasa Katkısı</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="{{ $label }}">Hediye için ziyaret</label>
                <input type="number" name="stamp_goal" min="0" max="50" value="{{ old('stamp_goal', $partner?->stamp_goal ?? 0) }}" class="{{ $input }}">
                <p class="{{ $hint }}">0 = damga kartı yok</p>
            </div>
            <div>
                <label class="{{ $label }}">Hediye</label>
                <input name="stamp_reward" value="{{ old('stamp_reward', $partner?->stamp_reward) }}" maxlength="120" placeholder="1 kahve bizden" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">Kasaya katkı (₺ / ziyaret)</label>
                <input type="number" name="kasa_contribution" step="0.01" min="0" max="10000" value="{{ old('kasa_contribution', $partner?->kasa_contribution) }}" placeholder="5" class="{{ $input }}">
                <p class="{{ $hint }}">Ziyaret başı işletmenin taahhüt ettiği tutar</p>
            </div>
        </div>
    </div>

    <div>
        <label class="{{ $label }}">Okutma PIN'i</label>
        <input name="pin" inputmode="numeric" pattern="[0-9]{4,8}" maxlength="8" autocomplete="new-password" placeholder="{{ $partner?->pin_hash ? '•••• (kayıtlı)' : '4–8 haneli rakam' }}" class="{{ $input }} font-mono sm:max-w-xs">
        <p class="{{ $hint }}">4–8 haneli rakam. {{ $partner ? 'Boş bırakırsan değişmez.' : '' }}
            @if ($partner && ! $partner->pin_hash)<span class="font-semibold text-rose-600">Henüz PIN yok — işletme okutma sayfasına giriş yapamaz.</span>@endif
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Logo</label>
            <img id="logo-preview" src="{{ $partner?->logoUrl() }}" class="mt-2 h-20 w-20 rounded-xl object-cover {{ $partner?->logoUrl() ? '' : 'hidden' }}">
            <input type="file" name="logo" accept="image/*" class="{{ $file }}" onchange="gtPreview(this, 'logo-preview')">
            <p class="{{ $hint }}">Kare (1:1), en fazla 4 MB</p>
        </div>
        <div>
            <label class="{{ $label }}">Kapak Görseli</label>
            <img id="cover-preview" src="{{ $partner?->coverUrl() }}" class="mt-2 h-20 w-full max-w-xs rounded-xl object-cover {{ $partner?->coverUrl() ? '' : 'hidden' }}">
            <input type="file" name="cover" accept="image/*" class="{{ $file }}" onchange="gtPreview(this, 'cover-preview')">
            <p class="{{ $hint }}">Yatay (16:9), en fazla 6 MB</p>
        </div>
    </div>

    <div class="sm:max-w-xs">
        <label class="{{ $label }}">Sıra</label>
        <input type="number" name="sort" value="{{ old('sort', $partner?->sort ?? 0) }}" min="0" class="{{ $input }}">
    </div>

    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
        <a href="{{ $partner ? route('admin.partners.show', $partner) : route('admin.partners.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>

<script>
    function gtPreview(input, id) {
        const img = document.getElementById(id);
        if (input.files && input.files[0]) {
            img.src = URL.createObjectURL(input.files[0]);
            img.classList.remove('hidden');
        }
    }
</script>
