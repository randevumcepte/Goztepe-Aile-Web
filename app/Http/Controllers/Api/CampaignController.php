<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InAppMessage;
use App\Services\Notifications\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function __construct(private readonly CampaignService $campaigns)
    {
    }

    /** Uygulama/web açılışında gösterilecek aktif pop-up. */
    public function active(Request $request): JsonResponse
    {
        $message = $this->campaigns->nextFor($request->user());

        if (! $message) {
            return response()->json(['message' => null]);
        }

        $this->campaigns->recordImpression($request->user(), $message);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'type' => $message->type,
                'title' => $message->title,
                'content' => $message->content,
                'media' => $message->media_path,
                'cta_label' => $message->cta_label,
                'cta_url' => $message->cta_url,
            ],
        ]);
    }

    public function click(Request $request, InAppMessage $message): JsonResponse
    {
        $this->campaigns->recordClick($request->user(), $message);

        return response()->json(['message' => 'ok']);
    }
}
