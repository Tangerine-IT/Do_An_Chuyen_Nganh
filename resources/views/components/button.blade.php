@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-primary-700 text-white shadow-soft hover:bg-primary-800',
        'accent' => 'bg-accent-500 text-primary-950 shadow-soft hover:bg-accent-400',
        'secondary' => 'bg-white text-primary-800 ring-1 ring-inset ring-stroke hover:bg-cream-50',
        'ghost' => 'text-primary-800 hover:bg-primary-100',
        'danger' => 'bg-red-700 text-white shadow-soft hover:bg-red-800',
    ];

    $sizes = [
        'sm' => 'px-4 py-1.5 text-sm',
        'md' => 'px-6 py-2.5 text-sm',
        'lg' => 'px-8 py-3.5 text-base',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full font-semibold transition duration-150 disabled:cursor-not-allowed disabled:opacity-50 '
        .($variants[$variant] ?? $variants['primary']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
