@props([
    'rating' => 0,
    'count' => null,
    'size' => 'h-4 w-4',
])

@php
    $rating = max(0, min(5, (float) $rating));
@endphp

<div {{ $attributes->class('inline-flex items-center gap-1.5') }}>
    <span class="inline-flex" role="img" aria-label="{{ __('ui.rating.label', ['rating' => number_format($rating, 1, ',', '.')]) }}">
        @for ($i = 0; $i < 5; $i++)
            <span class="relative inline-block shrink-0 {{ $size }}">
                <x-icon name="star" class="absolute inset-0 {{ $size }} text-cream-300" />
                <span class="absolute inset-y-0 left-0 overflow-hidden" style="width: {{ max(0, min(1, $rating - $i)) * 100 }}%">
                    <x-icon name="star" class="max-w-none shrink-0 {{ $size }} text-accent-500" />
                </span>
            </span>
        @endfor
    </span>

    @if ($count !== null)
        <span class="text-xs text-ink-muted">({{ $count }})</span>
    @endif
</div>
