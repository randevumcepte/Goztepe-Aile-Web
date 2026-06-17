<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    private array $categories = [
        'haber' => 'Haber', 'mac' => 'Maç', 'tribun' => 'Tribün',
        'duyuru' => 'Duyuru', 'basin' => 'Basın',
    ];

    public function index(): JsonResponse
    {
        $posts = Post::latest()->paginate(15);

        return response()->json([
            'items' => collect($posts->items())->map(fn ($m) => $this->payload($m))->values(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'total' => $posts->total(),
        ]);
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'categories' => ['haber', 'mac', 'tribun', 'duyuru', 'basin'],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['user_id'] = $request->user()->id;
        $data['published_at'] = $request->boolean('is_published') ? now() : null;

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('posts', 'uploads');
        }

        $post = Post::create($data);

        return response()->json([
            'message' => 'Haber yayınlandı.',
            'item' => $this->payload($post),
        ], 201);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $data = $this->validateData($request);
        $data['published_at'] = $request->boolean('is_published') ? ($post->published_at ?? now()) : null;

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('posts', 'uploads');
        }

        $post->update($data);

        return response()->json([
            'message' => 'Haber güncellendi.',
            'item' => $this->payload($post),
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(['message' => 'Haber silindi.']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'in:'.implode(',', array_keys($this->categories))],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'cover' => ['nullable', 'image', 'max:5120'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'haber';
        $slug = $base;
        $i = 2;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function payload(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'category' => $post->category,
            'category_label' => $post->categoryLabel(),
            'excerpt' => $post->excerpt,
            'body' => $post->body,
            'cover' => $post->coverUrl(),
            'is_published' => (bool) $post->is_published,
            'published_at' => $post->published_at?->toIso8601String(),
            'created_at' => $post->created_at?->toIso8601String(),
        ];
    }
}
