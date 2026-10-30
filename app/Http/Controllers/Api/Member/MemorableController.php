<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Chant;
use App\Models\Memorable;
use Illuminate\Http\JsonResponse;

class MemorableController extends Controller
{
    /** Unutulmazlar kartları (kategoriye göre gruplu gösterim için kategori bilgisiyle). */
    public function index(): JsonResponse
    {
        return response()->json([
            'categories' => collect(Memorable::CATEGORIES)->map(fn ($l, $k) => ['key' => $k, 'label' => $l])->values(),
            'items' => Memorable::active()->get()->map(fn (Memorable $m) => [
                'id' => $m->id,
                'category' => $m->category,
                'category_label' => $m->categoryLabel(),
                'name' => $m->name,
                'subtitle' => $m->subtitle,
                'era' => $m->era,
                'summary' => $m->summary,
                'cover' => $m->coverUrl(),
                'photo_count' => count($m->photos()),
            ])->values(),
        ]);
    }

    /** Detay: özgeçmiş, istatistikler, kaydırılabilir galeri. */
    public function show(Memorable $memorable): JsonResponse
    {
        abort_unless($memorable->is_active, 404);

        return response()->json([
            'id' => $memorable->id,
            'category' => $memorable->category,
            'category_label' => $memorable->categoryLabel(),
            'name' => $memorable->name,
            'subtitle' => $memorable->subtitle,
            'era' => $memorable->era,
            'summary' => $memorable->summary,
            'bio' => $memorable->bio,
            'stats' => array_values($memorable->stats ?? []),
            'cover' => $memorable->coverUrl(),
            'photos' => $memorable->photos(),
        ]);
    }

    /** Bestelerimiz: dokununca çalan besteler + sözleri. */
    public function chants(): JsonResponse
    {
        return response()->json([
            'items' => Chant::active()->get()->map(fn (Chant $c) => [
                'id' => $c->id,
                'title' => $c->title,
                'note' => $c->note,
                'lyrics' => $c->lyrics,
                'audio_url' => $c->audioUrl(),
            ])->values(),
        ]);
    }
}
