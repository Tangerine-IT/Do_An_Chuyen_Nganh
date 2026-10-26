<x-app-layout :title="$title" :description="$description">
    <div class="container-page py-10 sm:py-14">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-accent-700">{{ __('ui.catalog.eyebrow') }}</p>
            <h1 class="mt-2 text-4xl sm:text-5xl">{{ $title }}</h1>
            <p class="mt-4 text-lg text-ink-muted">{{ $description }}</p>
        </div>

        <div class="mt-10">
            @if (isset($categoryId) || isset($brandId))
                <livewire:product-catalog :category-id="$categoryId ?? ''" :brand-id="$brandId ?? ''" />
            @else
                <livewire:product-catalog />
            @endif
        </div>
    </div>
</x-app-layout>
