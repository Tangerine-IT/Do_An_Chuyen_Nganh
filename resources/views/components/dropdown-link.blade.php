@props(['icon' => null])

<a {{ $attributes->class('flex w-full items-center gap-2.5 px-4 py-2.5 text-start text-sm font-medium text-ink transition hover:bg-primary-50 hover:text-primary-800 focus-visible:bg-primary-50') }}>
    @if ($icon)
        <x-icon :name="$icon" class="h-5 w-5 text-primary-600" />
    @endif
    {{ $slot }}
</a>
