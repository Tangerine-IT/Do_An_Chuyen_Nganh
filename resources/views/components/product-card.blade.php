@props(['product'])

{{-- Expects primaryImage and brand to be eager loaded; rating_avg / rating_count come from Product::withRatingSummary(). --}}
@php
    $image = storage_url($product->primaryImage?->path);
    $inStock = $product->stock > 0;
    $discount = $product->is_on_sale ? (int) round((1 - $product->sale_price / $product->price) * 100) : 0;
    $url = route('products.show', $product->slug);
@endphp

<article {{ $attributes->class('group relative flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-line transition duration-200 hover:-translate-y-1 hover:shadow-lift') }}>
    <a href="{{ $url }}" tabindex="-1" aria-hidden="true" class="relative block aspect-square overflow-hidden bg-cream-100">
        @if ($image)
            <img
                src="{{ $image }}"
                alt=""
                loading="lazy"
                decoding="async"
                @class(['h-full w-full object-cover transition duration-300 group-hover:scale-105', 'opacity-60' => ! $inStock])
            >
        @else
            <span class="flex h-full w-full items-center justify-center text-primary-300">
                <x-icon name="paw" class="h-16 w-16" />
            </span>
        @endif
    </a>

    <div class="absolute left-3 top-3 flex flex-col items-start gap-1.5">
        @if ($discount > 0)
            <x-badge variant="accent">-{{ $discount }}%</x-badge>
        @endif
        @unless ($inStock)
            <x-badge variant="neutral">{{ __('ui.product.out_of_stock') }}</x-badge>
        @endunless
        @if (isset($product->recommendation) && $product->recommendation['status'] !== 'neutral')
            <x-badge title="{{ implode(' ', $product->recommendation['reasons']) }}" variant="{{ $product->recommendation['status'] === 'recommended' ? 'success' : 'danger' }}">
                {{ $product->recommendation['status'] === 'recommended' ? __('ui.recommendation.recommended') : __('ui.recommendation.warning') }}
            </x-badge>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-2 p-4" @auth x-data="{ wishlisted: {{ auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'true' : 'false' }} }" @endauth>
        <div class="flex items-center justify-between gap-2">
            <p class="truncate text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ $product->brand?->name }}</p>
            <x-pet-badge :species="$product->species" />
        </div>

        <h3 class="line-clamp-2 min-h-[2.75rem] font-heading text-base font-bold leading-snug text-primary-900">
            {{-- Stretched link: the whole card is clickable, but there is a single link for keyboard and screen readers. --}}
            <a href="{{ $url }}" class="after:absolute after:inset-0 after:content-['']">{{ $product->name }}</a>
        </h3>

        @if (($product->rating_count ?? 0) > 0)
            <x-rating-stars :rating="$product->rating_avg" :count="$product->rating_count" />
        @else
            <p class="text-xs text-ink-muted">{{ __('ui.rating.none') }}</p>
        @endif

        <x-price :product="$product" class="mt-auto pt-1" />
        @auth
            <button type="button" class="relative z-10 self-end text-sm font-semibold text-primary-700" @click="fetch('{{ route('wishlist.toggle', $product) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(response => response.json()).then(data => wishlisted = data.wishlisted)" x-text="wishlisted ? '♥ {{ __('ui.wishlist.saved') }}' : '♡ {{ __('ui.wishlist.add') }}'"></button>
        @endauth

        @isset($actions)
            <div class="relative z-10 pt-2">{{ $actions }}</div>
        @endisset
    </div>
</article>
