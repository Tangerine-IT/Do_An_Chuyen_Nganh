<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::query()->published()
            ->when($request->filled('category'), fn (Builder $query) => $query->where('category', $request->string('category')))
            ->when($request->filled('q'), fn (Builder $query) => $query->where(fn (Builder $search) => $search
                ->where('title', 'like', '%'.$request->string('q').'%')
                ->orWhere('excerpt', 'like', '%'.$request->string('q').'%')))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('blog.index', [
            'posts' => $posts,
            'categories' => Post::query()->published()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::query()->published()->where('slug', $slug)->firstOrFail();
        $relatedPosts = Post::query()->published()
            ->whereKeyNot($post->id)
            ->when($post->category, fn (Builder $query) => $query->where('category', $post->category))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
