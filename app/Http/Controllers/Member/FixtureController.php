<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Services\TeamBadgeService;
use Illuminate\View\View;

class FixtureController extends Controller
{
    public function index(TeamBadgeService $badges): View
    {
        $upcoming = Fixture::upcoming()->get();
        $played = Fixture::played()->limit(20)->get();

        // Logosu eksik maçlar için rakip armasını otomatik çek ve kaydet (kendi kendini onarır)
        foreach ($upcoming->merge($played) as $f) {
            if (! $f->opponent_logo_path) {
                try {
                    if ($badge = $badges->badgeUrl($f->opponent)) {
                        $f->update(['opponent_logo_path' => $badge]);
                    }
                } catch (\Throwable $e) {
                    // sessiz geç
                }
            }
        }

        $goztepeBadge = $badges->goztepeBadgeUrl();

        return view('member.fikstur', compact('upcoming', 'played', 'goztepeBadge'));
    }
}
