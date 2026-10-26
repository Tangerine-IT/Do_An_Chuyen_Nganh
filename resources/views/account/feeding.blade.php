<x-app-layout :title="__('ui.feeding.title')">
    <div class="container-page py-10">
        <h1 class="text-3xl">{{ __('ui.feeding.title') }}</h1>
        <p class="mt-2 text-sm text-ink-muted">{{ __('ui.feeding.approximate') }}</p>
        <x-card class="mt-6 max-w-2xl"><form method="POST" action="{{ route('feeding.store') }}" class="grid gap-4 sm:grid-cols-3">@csrf<x-select name="pet_id" :label="__('ui.feeding.pet')" :options="$pets->mapWithKeys(fn($pet) => [$pet->id => $pet->name])->all()" required /><x-select name="product_id" :label="__('ui.feeding.product')" :options="$products->mapWithKeys(fn($product) => [$product->id => $product->name])->all()" required /><x-button type="submit" class="self-end">{{ __('ui.feeding.save') }}</x-button></form></x-card>
        <div class="mt-8 grid gap-4 md:grid-cols-2">@forelse($estimates as $estimate)<x-card><h2 class="font-semibold">{{ $estimate->pet->name }} · {{ $estimate->product->name }}</h2><p class="mt-2 text-sm text-ink-muted">{{ __('ui.feeding.daily') }}: {{ $estimate->daily_grams }}g</p><p class="text-sm text-ink-muted">{{ __('ui.feeding.runs_out') }}: {{ $estimate->runs_out_at->format('d/m/Y') }}</p><form method="POST" action="{{ route('feeding.reorder', $estimate) }}" class="mt-4">@csrf<x-button type="submit" size="sm">{{ __('ui.feeding.reorder') }}</x-button></form></x-card>@empty<x-empty-state :title="__('ui.feeding.empty')" />@endforelse</div>
    </div>
</x-app-layout>
