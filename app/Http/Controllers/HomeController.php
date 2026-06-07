<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Slider;
use App\Models\Sponsor;
use App\Services\LedgerService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly LedgerService $ledger)
    {
    }

    public function index(): View
    {
        $posts = Post::query()->published()->latest('published_at')->latest('id')->limit(7)->get();

        $summary = $this->ledger->publicSummary(5);

        return view('home', [
            'sliders' => Slider::active()->get(),
            'sponsors' => Sponsor::active()->get(),
            'featured' => $posts->first(),
            'secondary' => $posts->slice(1, 2),
            'rest' => $posts->slice(3, 4),
            'totals' => $summary['totals'],
        ]);
    }
}
