@props([
    'title',
    'description' => null,
    'icon' => 'paw',
])

<div {{ $attributes->class('flex flex-col items-center rounded-2xl border-2 border-dashed border-primary-200 bg-white/60 px-6 py-12 text-center') }}>
    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 text-primary-700">
        <x-icon :name="$icon" class="h-8 w-8" />
    </span>

    <h3 class="mt-4 text-lg">{{ $title }}</h3>

    @if ($description)
        <p class="mt-1 max-w-md text-sm text-ink-muted">{{ $description }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-6">{{ $slot }}</div>
    @endif
</div>
