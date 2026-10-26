<x-admin-layout :title="$post->exists ? __('ui.common.edit') : __('ui.blog.create')">
    <h1 class="text-3xl">{{ $post->exists ? __('ui.common.edit') : __('ui.blog.create') }}</h1>
    <x-card class="mt-6 max-w-4xl">
        <form method="POST" enctype="multipart/form-data" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}" class="space-y-5">
            @csrf @if($post->exists) @method('PUT') @endif
            <x-input name="title" :label="__('ui.blog.article_title')" :value="old('title', $post->title)" required />
            <div class="grid gap-5 sm:grid-cols-2"><x-input name="slug" :label="__('ui.blog.slug')" :value="old('slug', $post->slug)" /><x-input name="category" :label="__('ui.blog.category')" :value="old('category', $post->category)" /></div>
            <x-input name="excerpt" :label="__('ui.blog.excerpt')" :value="old('excerpt', $post->excerpt)" />
            <div><label class="form-label" for="body">{{ __('ui.blog.body') }}</label><textarea id="body" name="body" rows="16" class="form-control font-mono" required>{{ old('body', $post->body) }}</textarea><p class="mt-1 text-xs text-ink-muted">{{ __('ui.blog.markdown_hint') }}</p></div>
            <div class="grid gap-5 sm:grid-cols-2"><x-select name="status" :label="__('ui.admin.status')" :options="['draft' => __('enums.post_status.draft'), 'published' => __('enums.post_status.published')]" :value="old('status', $post->status?->value)" /><x-input type="datetime-local" name="published_at" :label="__('ui.blog.published_at')" :value="old('published_at', $post->published_at?->format('Y-m-d\TH:i'))" /></div>
            <x-input type="file" name="cover_image" :label="__('ui.blog.cover_image')" accept="image/*" />
            <div class="grid gap-5 sm:grid-cols-2"><x-input name="meta_title" :label="__('ui.blog.meta_title')" :value="old('meta_title', $post->meta_title)" /><x-input name="meta_description" :label="__('ui.blog.meta_description')" :value="old('meta_description', $post->meta_description)" /></div>
            <x-button type="submit">{{ __('ui.common.save') }}</x-button>
        </form>
    </x-card>
</x-admin-layout>
