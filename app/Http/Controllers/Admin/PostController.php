<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    private array $categories = [
        'haber' => 'Haber', 'mac' => 'Maç', 'tribun' => 'Tribün',
        'duyuru' => 'Duyuru', 'basin' => 'Basın',
    ];

    public function index(): View
    {
        $posts = Post::latest()->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.posts.create', ['categories' => $this->categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['user_id'] = $request->user()->id;
        $data['published_at'] = $request->boolean('is_published') ? now() : null;

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('posts', 'uploads');
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('status', 'Haber yayınlandı.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', ['post' => $post, 'categories' => $this->categories]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['published_at'] = $request->boolean('is_published') ? ($post->published_at ?? now()) : null;

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('posts', 'uploads');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('status', 'Haber güncellendi.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return back()->with('status', 'Haber silindi.');
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
}
