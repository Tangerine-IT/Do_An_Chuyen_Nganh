{{-- Loading placeholder with the same footprint as x-product-card. --}}
<div {{ $attributes->class('flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-line') }} aria-busy="true">
    <x-skeleton class="aspect-square w-full rounded-none" />
    <div class="flex flex-1 flex-col gap-3 p-4">
        <x-skeleton class="h-3 w-1/3" />
        <x-skeleton class="h-4 w-full" />
        <x-skeleton class="h-4 w-2/3" />
        <x-skeleton class="mt-auto h-6 w-1/2" />
    </div>
</div>
