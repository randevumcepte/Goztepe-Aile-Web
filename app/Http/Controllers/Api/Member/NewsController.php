<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    /** Yayınlanmış haberler — sayfalı. */
    public function index(): JsonResponse
    {
        $posts = Post::published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12);

        return response()->json([
            'items' => collect($posts->items())->map(fn ($p) => $this->card($p))->values(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'total' => $posts->total(),
        ]);
    }

    /** Tek haber detayı (slug ile). */
    public function show(Post $post): JsonResponse
    {
        abort_unless($post->is_published, 404);

        return response()->json([
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'category' => $post->category,
            'category_label' => $post->categoryLabel(),
            'excerpt' => $post->excerpt,
            'body' => $post->body,
            'cover' => $post->coverUrl(),
            'published_at' => $post->published_at?->toIso8601String(),
        ]);
    }

    private function card(Post $p): array
    {
        return [
            'id' => $p->id,
            'title' => $p->title,
            'slug' => $p->slug,
            'category' => $p->category,
            'category_label' => $p->categoryLabel(),
            'excerpt' => $p->excerpt,
            'cover' => $p->coverUrl(),
            'published_at' => $p->published_at?->toIso8601String(),
        ];
    }
}
