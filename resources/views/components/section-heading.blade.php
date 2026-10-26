@props([
    'title',
    'subtitle' => null,
    'href' => null,
    'linkLabel' => null,
])

<div {{ $attributes->class('mb-8 flex flex-wrap items-end justify-between gap-x-6 gap-y-2') }}>
    <div class="max-w-2xl">
        <h2 class="text-2xl sm:text-3xl">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-1.5 text-ink-muted">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($href)
        <a href="{{ $href }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-700 hover:text-primary-900">
            {{ $linkLabel ?? __('ui.common.view_all') }}
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    @endif
</div>
