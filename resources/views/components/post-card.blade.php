@props(['post'])

@php
    $image = storage_url($post->cover_image_path);
    $url = route('blog.show', $post->slug);
@endphp

<article {{ $attributes->class('group relative flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-line transition duration-200 hover:-translate-y-1 hover:shadow-lift') }}>
    <div class="aspect-[16/10] overflow-hidden bg-cream-100">
        @if ($image)
            <img src="{{ $image }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <span class="flex h-full w-full items-center justify-center text-primary-300">
                <x-icon name="paw" class="h-14 w-14" />
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-2 p-5">
        <time datetime="{{ $post->published_at?->toDateString() }}" class="text-xs font-semibold uppercase tracking-wide text-ink-muted">
            {{ $post->published_at?->translatedFormat('d/m/Y') }}
        </time>

        <h3 class="line-clamp-2 font-heading text-lg font-bold leading-snug text-primary-900">
            <a href="{{ $url }}" class="after:absolute after:inset-0 after:content-['']">{{ $post->title }}</a>
        </h3>

        <p class="line-clamp-3 text-sm text-ink-muted">{{ $post->excerpt }}</p>

        <span class="mt-auto inline-flex items-center gap-1 pt-2 text-sm font-semibold text-primary-700">
            {{ __('ui.common.read_more') }}
            <x-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1" />
        </span>
    </div>
</article>
