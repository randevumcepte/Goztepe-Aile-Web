<?php

namespace App\Http\Controllers;

use App\Models\MembershipFeature;
use App\Models\MembershipPlan;
use Database\Seeders\MembershipSeeder;
use Illuminate\View\View;

class PageController extends Controller
{
    public function hakkimizda(): View
    {
        return view('pages.hakkimizda');
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
