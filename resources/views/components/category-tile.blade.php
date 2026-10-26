@props(['category'])

@php
    $image = storage_url($category->image_path);
@endphp

<a href="{{ route('categories.show', $category->slug) }}" {{ $attributes->class('group flex flex-col items-center gap-3 rounded-2xl bg-white p-4 text-center shadow-soft ring-1 ring-line transition duration-200 hover:-translate-y-1 hover:shadow-lift') }}>
    <span class="flex aspect-square w-full items-center justify-center overflow-hidden rounded-xl bg-cream-100">
        @if ($image)
            <img src="{{ $image }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <x-icon name="paw" class="h-12 w-12 text-primary-300" />
        @endif
    </span>
    <span class="font-heading text-sm font-bold leading-tight text-primary-900 sm:text-base">{{ $category->name }}</span>
</a>
