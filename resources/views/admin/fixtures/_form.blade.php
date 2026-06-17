@php
    $input = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $label = 'block text-sm font-medium text-slate-700';
    $fixture = $fixture ?? null;
    $kickoff = old('kickoff_at', $fixture?->kickoff_at?->format('Y-m-d\TH:i'));
@endphp

<div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Rakip Takım</label>
            <input name="opponent" value="{{ old('opponent', $fixture?->opponent) }}" required placeholder="Örn. Fenerbahçe" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Turnuva / Lig</label>
            <input name="competition" value="{{ old('competition', $fixture?->competition ?? 'Süper Lig') }}" required class="{{ $input }}">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Maç Tarihi & Saati</label>
            <input type="datetime-local" name="kickoff_at" value="{{ $kickoff }}" required class="{{ $input }}">
        </div>
        <div class="flex items-end">
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                <input type="checkbox" name="is_home" value="1" @checked(old('is_home', $fixture?->is_home ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Ev sahibiyiz (Göztepe evinde)
            </label>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $label }}">Stat / Saha</label>
            <input name="venue" value="{{ old('venue', $fixture?->venue) }}" placeholder="Örn. Gürsel Aksel Stadyumu" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Yayıncı (TV)</label>
            <input name="broadcast" value="{{ old('broadcast', $fixture?->broadcast) }}" placeholder="Örn. beIN Sports 1" class="{{ $input }}">
        </div>
    </div>

    <div>
        <label class="{{ $label }}">Rakip Logosu (isteğe bağlı)</label>
        @if ($fixture?->opponentLogoUrl())<img src="{{ $fixture->opponentLogoUrl() }}" class="mt-2 h-16 object-contain">@endif
        <input type="file" name="opponent_logo" accept="image/*" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
    </div>

    <div class="rounded-lg bg-slate-50 p-4">
        <p class="text-sm font-medium text-slate-700">Skor <span class="font-normal text-slate-400">(maç oynandıysa doldur — boş bırakırsan "yaklaşan maç" sayılır)</span></p>
        <div class="mt-2 grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-slate-500">Göztepe</label>
                <input type="number" name="home_score" value="{{ old('home_score', $fixture?->home_score) }}" min="0" max="99" class="{{ $input }}">
            </div>
            <div>
                <label class="text-xs text-slate-500">Rakip</label>
                <input type="number" name="away_score" value="{{ old('away_score', $fixture?->away_score) }}" min="0" max="99" class="{{ $input }}">
            </div>
        </div>
    </div>

    <div>
        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $fixture?->is_active ?? true)) class="rounded text-brand-600 focus:ring-brand-500"> Aktif (sitede gösterilsin)
        </label>
    </div>

    <div class="flex gap-3 border-t border-slate-100 pt-5">
        <button class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Kaydet</button>
        <a href="{{ route('admin.fixtures.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">İptal</a>
    </div>
</div>
