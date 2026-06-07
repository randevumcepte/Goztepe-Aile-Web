<?php

namespace App\Http\Controllers;

use App\Models\Post;
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

        $featured = $posts->first();
        $secondary = $posts->slice(1, 2);
        $rest = $posts->slice(3, 4);

        $summary = $this->ledger->publicSummary(5);

        return view('home', [
            'featured' => $featured,
            'secondary' => $secondary,
            'rest' => $rest,
            'totals' => $summary['totals'],
        ]);
    }
}
