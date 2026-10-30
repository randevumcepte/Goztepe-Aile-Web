@extends('layouts.admin')
@section('title', 'Sistem Araçları')

@php
    $checks = [
        ['FCM servis hesabı dosyası (push bildirim)', $fcmOk, $fcmOk ? 'Dosya bulundu.' : 'Dosya yok: storage/app/fcm-service-account.json (veya FCM_CREDENTIALS). Push bildirimler cihazlara gitmez.'],
        ['Anthropic AI anahtarı', $aiOk, $aiOk ? 'Tanımlı.' : 'Tanımlı değil (ANTHROPIC_API_KEY). AI metinleri yerine kural metinleri kullanılır.'],
    ];
    foreach ($schedule as $s) {
        $checks[] = [
            'Zamanlayıcı: '.$s['label'],
            $s['ok'],
            ($s['ago'] ? 'Son çalışma '.$s['ago'] : 'Hiç çalışmadı').($s['ok'] ? '.' : ' — cron satırı eksik olabilir (eşik '.$s['threshold'].' dk).'),
        ];
    }
    $checks[] = ['Sizin kayıtlı cihazınız', $myDevices > 0, $myDevices.' cihaz'.($myDevices > 0 ? '.' : ' — mobil uygulamaya bu hesapla giriş yapın.')];
    $checks[] = ['Toplam kayıtlı cihaz', $allDevices > 0, $allDevices.' cihaz.'];
    if ($demoPartners !== null) {
        $checks[] = ['Demo ayrıcalık partneri', $demoPartners === 0, $demoPartners === 0 ? 'Demo kayıt yok.' : $demoPartners.' demo partner var — yayından önce silin (ayricalik:demo-sil).'];
    }
    $commands = [
        ['/opt/php83/bin/php artisan schedule:list', 'Zamanlanmış görevleri ve sıradaki çalışma zamanlarını listeler.'],
        ['/opt/php83/bin/php artisan push:test EPOSTA', 'Belirtilen e-postadaki üyeye test push gönderir, cihaz bazında sonucu yazar.'],
        ['/opt/php83/bin/php artisan ayricalik:demo-sil', 'Demo ayrıcalık partnerlerini siler.'],
    ];
@endphp

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-900">Durum Kontrolleri</h2>
        </div>
        <ul class="divide-y divide-slate-100">
            @foreach ($checks as [$label, $ok, $detail])
                <li class="flex items-start gap-3 px-5 py-3">
                    <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full text-sm font-bold {{ $ok ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $ok ? '✓' : '✗' }}</span>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $label }}</p>
                        <p class="text-xs text-slate-500">{{ $detail }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
        @if (collect($schedule)->contains(fn ($s) => ! $s['ok']))
            <p class="border-t border-slate-100 px-5 py-3 text-xs text-rose-700">
                Cron çalışmıyor olabilir: sunucuda
                <code class="rounded bg-rose-50 px-1 py-0.5 font-mono text-[11px] text-rose-900">* * * * * cd /var/www/www-root/data/www/randevumcepteyenilaravel &amp;&amp; /opt/php83/bin/php artisan schedule:run</code>
                satırı olmalı.
            </p>
        @endif
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-slate-900">Test Bildirimi</h2>
        <p class="mt-1 text-xs text-slate-500">Bildirim yalnızca size (giriş yapan yönetici) gönderilir.</p>
        <div class="mt-4 flex flex-wrap gap-3">
            <form method="POST" action="{{ route('admin.tools.test-push') }}">
                @csrf
                <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Bana test bildirimi gönder</button>
            </form>
            <form method="POST" action="{{ route('admin.tools.test-goal') }}">
                @csrf
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Bana gol bildirimi gönder (tribün sesli)</button>
            </form>
        </div>
        <p class="mt-4 rounded-lg bg-amber-50 px-3 py-2 text-xs leading-relaxed text-amber-900">
            Bildirimin gelmesi için bu yönetici hesabıyla telefonunuzdaki mobil uygulamaya <b>en az bir kez giriş yapmış</b> olmanız gerekir; cihaz ancak böyle kaydedilir.
            Şu an kayıtlı cihazınız: <b>{{ $myDevices }}</b>. Gol bildirimi Android'de tribün sesli özel kanaldan çalar.
        </p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" x-data="{ copied: null }">
        <h2 class="font-bold text-slate-900">Faydalı Sunucu Komutları</h2>
        <p class="mt-1 text-xs text-slate-500">Proje klasöründe (<code class="font-mono">cd /var/www/www-root/data/www/randevumcepteyenilaravel</code>) çalıştırın.</p>
        <ul class="mt-4 space-y-3">
            @foreach ($commands as $i => [$cmd, $desc])
                <li>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 overflow-x-auto whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 font-mono text-xs text-slate-100">{{ $cmd }}</code>
                        <button type="button"
                                @click="navigator.clipboard.writeText(@js($cmd)).then(() => { copied = {{ $i }}; setTimeout(() => copied = null, 1500) })"
                                class="shrink-0 rounded-md border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50"
                                x-text="copied === {{ $i }} ? 'Kopyalandı' : 'Kopyala'">Kopyala</button>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ $desc }}</p>
                </li>
            @endforeach
        </ul>
    </section>
</div>
@endsection
