<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\InAppMessage;
use App\Services\Notifications\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function __construct(private readonly CampaignService $campaigns)
    {
    }

    /** Pop-up butonuna tıklayınca: tıklamayı say, hedef sayfaya (varsayılan bağış) yönlendir. */
    public function go(Request $request, InAppMessage $message): RedirectResponse
    {
        $this->campaigns->recordClick($request->user(), $message);

        $url = $message->cta_url ?: route('uye.bagis');

        return redirect()->to($url);
    }
}
