@props([
    'variant' => 'neutral',
    'size' => 'md',
])

@php
    $variants = [
        'neutral' => 'bg-cream-200 text-primary-900',
        'primary' => 'bg-primary-100 text-primary-900',
        'accent' => 'bg-accent-500 text-primary-950',
        'success' => 'bg-emerald-100 text-emerald-900',
        'warning' => 'bg-amber-100 text-amber-900',
        'danger' => 'bg-red-100 text-red-900',
        'info' => 'bg-sky-100 text-sky-900',
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-[11px]',
        'md' => 'px-2.5 py-1 text-xs',
    ];
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1 whitespace-nowrap rounded-full font-semibold '.($variants[$variant] ?? $variants['neutral']).' '.($sizes[$size] ?? $sizes['md'])) }}>
    {{ $slot }}
</span>
