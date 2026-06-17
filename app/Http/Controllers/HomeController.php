<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\MembershipPlan;
use App\Models\Post;
use App\Models\Slider;
use App\Models\Sponsor;
use App\Services\LedgerService;
use Database\Seeders\DemoFixturesSeeder;
use Database\Seeders\DemoPostsSeeder;
use Database\Seeders\MembershipSeeder;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly LedgerService $ledger)
    {
    }

    public function index(): View
    {
        // Üyelik verisi boşsa otomatik doldur (sunucuda manuel seed gerektirmez)
        try {
            if (MembershipPlan::query()->doesntExist()) {
                app(MembershipSeeder::class)->run();
            }
        } catch (\Throwable $e) {
            // tablo henüz yoksa sessiz geç
        }

        // Haber yoksa ana sayfa boş görünmesin diye örnek haberleri otomatik doldur
        try {
            if (Post::query()->doesntExist()) {
                app(DemoPostsSeeder::class)->run();
            }
        } catch (\Throwable $e) {
            // tablo henüz yoksa sessiz geç
        }

        // Maç yoksa "Haftanın Maçı" kartı için örnek maç otomatik ekle
        try {
            if (Fixture::query()->doesntExist()) {
                app(DemoFixturesSeeder::class)->run();
            }
        } catch (\Throwable $e) {
            // tablo henüz yoksa sessiz geç
        }

        $posts = Post::query()->published()->latest('published_at')->latest('id')->limit(7)->get();

        $nextMatch = Fixture::upcoming()->first();

        $summary = $this->ledger->publicSummary(5);

        return view('home', [
            'sliders' => Slider::active()->get(),
            'sponsors' => Sponsor::active()->get(),
            'plans' => MembershipPlan::active()->get(),
            'featured' => $posts->first(),
            'secondary' => $posts->slice(1, 2),
            'latest' => $posts->take(3),
            'nextMatch' => $nextMatch,
            'totals' => $summary['totals'],
        ]);
    }
}
