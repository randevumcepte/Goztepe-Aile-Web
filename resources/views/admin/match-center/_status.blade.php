{{-- Zamanlanmış görev durumu şeridi. Beklenen: $status (MatchCenterController::systemStatus()) --}}
<div class="grid grid-cols-1 gap-3 md:grid-cols-2">
    @foreach ($status as $s)
        <div class="rounded-2xl border p-4 shadow-sm {{ $s['ok'] ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
            <div class="flex items-center gap-2">
                <span class="inline-block h-2.5 w-2.5 rounded-full {{ $s['ok'] ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                <p class="text-sm font-semibold {{ $s['ok'] ? 'text-emerald-800' : 'text-rose-800' }}">{{ $s['label'] }}</p>
                <span class="ml-auto text-xs {{ $s['ok'] ? 'text-emerald-700' : 'text-rose-700' }}">
                    son çalışma: {{ $s['ago'] ?? 'hiç çalışmadı' }}
                </span>
            </div>
            @unless ($s['ok'])
                <p class="mt-2 text-xs leading-relaxed text-rose-700">
                    Cron çalışmıyor olabilir: sunucuda
                    <code class="rounded bg-white/70 px-1 py-0.5 font-mono text-[11px] text-rose-900">* * * * * cd /var/www/www-root/data/www/randevumcepteyenilaravel &amp;&amp; /opt/php83/bin/php artisan schedule:run</code>
                    satırı olmalı.
                </p>
            @else
                <p class="mt-1 text-xs text-emerald-700">Çalışıyor (eşik {{ $s['threshold'] }} dk).</p>
            @endunless
        </div>
    @endforeach
</div>
