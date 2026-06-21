<?php

namespace App\Http\Controllers;

use App\Models\HistoryEvent;
use App\Models\Legend;
use App\Models\MembershipFeature;
use App\Models\MembershipPlan;
use Database\Seeders\HistorySeeder;
use Database\Seeders\LegendSeeder;
use Database\Seeders\MembershipSeeder;
use Illuminate\View\View;

class PageController extends Controller
{
    public function hakkimizda(): View
    {
        return view('pages.hakkimizda');
    }

    public function sanliTarihimiz(): View
    {
        try {
            if (HistoryEvent::query()->doesntExist()) {
                app(HistorySeeder::class)->run();
            }
        } catch (\Throwable $e) {
            // tablo henüz yoksa sessiz geç
        }

        try {
            if (Legend::query()->doesntExist()) {
                app(LegendSeeder::class)->run();
            }
        } catch (\Throwable $e) {
            // tablo henüz yoksa sessiz geç
        }

        $events = collect();
        $legends = collect();
        try {
            $events = HistoryEvent::active()->get();
            $legends = Legend::active()->get();
        } catch (\Throwable $e) {
            // migrate edilmemişse sayfa yine açılsın
        }

        return view('pages.sanli-tarihimiz', [
            'timeline' => $events->where('in_timeline', true)->values(),
            'gallery' => $events->where('in_gallery', true)->values(),
            'legends' => $legends,
        ]);
    }

    public function taraftarSesi(): View
    {
        return view('pages.taraftar-sesi');
    }

    public function iletisim(): View
    {
        return view('pages.iletisim');
    }

    public function uyelikAvantajlari(): View
    {
        try {
            if (MembershipPlan::query()->doesntExist()) {
                app(MembershipSeeder::class)->run();
            }
        } catch (\Throwable $e) {
            // tablo henüz yoksa sessiz geç
        }

        return view('pages.uyelik-avantajlari', [
            'plans' => MembershipPlan::active()->get(),
            'features' => MembershipFeature::active()->get(),
        ]);
    }
}
