<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\Post;
use App\Models\Slider;
use App\Models\Sponsor;
use App\Services\LedgerService;
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

        $posts = Post::query()->published()->latest('published_at')->latest('id')->limit(7)->get();

        $summary = $this->ledger->publicSummary(5);

        return view('home', [
            'sliders' => Slider::active()->get(),
            'sponsors' => Sponsor::active()->get(),
            'plans' => MembershipPlan::active()->get(),
            'featured' => $posts->first(),
            'secondary' => $posts->slice(1, 2),
            'rest' => $posts->slice(3, 4),
            'totals' => $summary['totals'],
        ]);
    }
}
