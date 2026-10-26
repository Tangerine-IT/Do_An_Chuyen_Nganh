<x-app-layout :title="$post->meta_title ?: $post->title" :description="$post->meta_description ?: $post->excerpt">
    @push('head')
        <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
        <meta property="og:description" content="{{ $post->meta_description ?: $post->excerpt }}">
        @if ($post->cover_image_path)<meta property="og:image" content="{{ storage_url($post->cover_image_path) }}">@endif
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
    @endpush
    <article class="container-page py-10 sm:py-14">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">← {{ __('ui.blog.back') }}</a>
            <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-ink-muted">
                @if ($post->category)<span class="rounded-full bg-primary-50 px-3 py-1 font-semibold text-primary-700">{{ $post->category }}</span>@endif
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('d/m/Y') }}</time>
                <span>· {{ $post->readingTime() }} {{ __('ui.blog.minutes') }}</span>
            </div>
            <h1 class="mt-4 text-4xl leading-tight sm:text-5xl">{{ $post->title }}</h1>
            <p class="mt-5 text-lg text-ink-muted">{{ $post->excerpt }}</p>
            @if ($post->cover_image_path)
                <img class="mt-8 aspect-[16/8] w-full rounded-2xl object-cover" src="{{ storage_url($post->cover_image_path) }}" alt="{{ $post->title }}">
            @endif
            <div class="prose prose-stone mt-10 max-w-none prose-headings:font-heading prose-a:text-primary-700">
                {!! $post->renderedBody() !!}
            </div>
            <div class="mt-10 border-t border-line pt-6">
                <p class="text-sm font-semibold text-ink-muted">{{ __('ui.blog.share') }}</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a class="rounded-full bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}">Facebook</a>
                    <a class="rounded-full bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700" target="_blank" rel="noopener" href="https://zalo.me/share?url={{ urlencode(route('blog.show', $post->slug)) }}">Zalo</a>
                    <button type="button" class="rounded-full bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700" onclick="navigator.clipboard.writeText(window.location.href)">{{ __('ui.blog.copy_link') }}</button>
                </div>
            </div>
        </div>
        @if ($relatedPosts->isNotEmpty())
            <section class="mx-auto mt-14 max-w-5xl">
                <h2 class="text-2xl">{{ __('ui.blog.related') }}</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-3">
                    @foreach ($relatedPosts as $relatedPost)<x-post-card :post="$relatedPost" />@endforeach
                </div>
            </section>
        @endif
    </article>
</x-app-layout>
