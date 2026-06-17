<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()->published()
            ->latest('published_at')->latest('id')
            ->paginate(12);

        return view('member.haberler', compact('posts'));
    }
}
