@props(['service'])

@php
    $image = storage_url($service->image_path);
@endphp

<article {{ $attributes->class('flex h-full flex-col rounded-2xl bg-white p-5 shadow-soft ring-1 ring-line') }}>
    <div class="flex items-center gap-4">
        <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-primary-100">
            @if ($image)
                <img src="{{ $image }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover">
            @else
                <x-icon name="paw" class="h-7 w-7 text-primary-600" />
            @endif
        </span>
        <h3 class="font-heading text-lg font-bold leading-snug text-primary-900">{{ $service->name }}</h3>
    </div>

    <p class="mt-3 line-clamp-3 text-sm text-ink-muted">{{ $service->description }}</p>

    <div class="mt-auto flex items-end justify-between gap-3 pt-5">
        <div>
            <p class="font-heading text-xl font-extrabold text-primary-800">{{ format_vnd($service->price) }}</p>
            <p class="text-xs text-ink-muted">{{ $service->duration_label }}</p>
        </div>
        <x-button :href="route('services.index')" size="sm" variant="secondary">{{ __('ui.home.services.book') }}</x-button>
    </div>
</article>
