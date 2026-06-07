<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()->published()->latest('published_at')->latest('id')->paginate(9);

        return view('news.index', compact('posts'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published, 404);

        $related = Post::query()->published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')->limit(3)->get();

        return view('news.show', compact('post', 'related'));
    }
}
