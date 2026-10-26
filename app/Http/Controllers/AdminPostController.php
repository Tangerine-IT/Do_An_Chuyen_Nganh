<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPostController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.posts.index', [
            'posts' => Post::query()
                ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%'))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.form', ['post' => new Post(['status' => PostStatus::Draft])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $post = Post::create($this->validated($request));
        $this->storeCover($request, $post);

        return redirect()->route('admin.posts.index')->with('success', __('ui.admin.saved'));
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $post->update($this->validated($request, $post));
        $this->storeCover($request, $post);

        return redirect()->route('admin.posts.index')->with('success', __('ui.admin.saved'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->cover_image_path) {
            Storage::disk('public')->delete($post->cover_image_path);
        }
        $post->delete();

        return back()->with('success', __('ui.admin.deleted'));
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug,'.($post?->id ?? 'NULL')],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['published_at'] = $data['status'] === PostStatus::Published->value
            ? ($data['published_at'] ?? now())
            : null;
        unset($data['cover_image']);
        $data['user_id'] = auth()->id();

        return $data;
    }

    private function storeCover(Request $request, Post $post): void
    {
        if (! $request->hasFile('cover_image')) {
            return;
        }
        if ($post->cover_image_path) {
            Storage::disk('public')->delete($post->cover_image_path);
        }
        $post->update(['cover_image_path' => $request->file('cover_image')->store('posts', 'public')]);
    }
}
