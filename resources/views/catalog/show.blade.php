<x-app-layout
    :title="$product->meta_title ?: $product->name"
    :description="$product->meta_description ?: ($product->short_description ?: __('ui.catalog.product_description', ['product' => $product->name]))"
    :image="storage_url($product->primaryImage?->path)"
    :canonical="route('products.show', $product->slug)"
>
    <div class="container-page py-10 sm:py-14">
        <nav class="mb-8 text-sm text-ink-muted" aria-label="{{ __('ui.catalog.breadcrumbs') }}">
            <a href="{{ route('products.index') }}" class="font-semibold text-primary-700 hover:underline">{{ __('ui.nav.all_products') }}</a>
            <span class="mx-2">/</span>
            <span>{{ $product->name }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2 lg:gap-14">
            <div x-data="{ selected: 0, lightbox: false }">
                <div class="relative aspect-square overflow-hidden rounded-3xl bg-cream-100 ring-1 ring-line">
                    @foreach ($product->images as $index => $image)
                        <img
                            x-show="selected === {{ $index }}"
                            x-cloak
                            src="{{ storage_url($image->path) }}"
                            alt="{{ $image->alt_text ?: $product->name }}"
                            class="h-full w-full cursor-zoom-in object-cover"
                            @click="lightbox = true"
                        >
                    @endforeach
                    @if ($product->images->isEmpty())
                        <div class="flex h-full items-center justify-center text-primary-300"><x-icon name="paw" class="h-24 w-24" /></div>
                    @endif
                </div>

                @if ($product->images->count() > 1)
                    <div class="mt-4 grid grid-cols-5 gap-3">
                        @foreach ($product->images as $index => $image)
                            <button type="button" class="aspect-square overflow-hidden rounded-xl ring-1 ring-line transition focus:ring-2 focus:ring-accent-500" :class="{ 'ring-2 ring-primary-700': selected === {{ $index }} }" @click="selected = {{ $index }}">
                                <img src="{{ storage_url($image->path) }}" alt="{{ $image->alt_text ?: $product->name }}" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif

                <div x-show="lightbox" x-cloak x-trap.noscroll="lightbox" class="fixed inset-0 z-[70] flex items-center justify-center bg-primary-950/90 p-4" @keydown.escape.window="lightbox = false">
                    <button type="button" class="absolute right-4 top-4 rounded-full bg-white p-3 text-primary-900" @click="lightbox = false"><span class="sr-only">{{ __('ui.common.close') }}</span><x-icon name="close" class="h-6 w-6" /></button>
                    @foreach ($product->images as $index => $image)
                        <img x-show="selected === {{ $index }}" src="{{ storage_url($image->path) }}" alt="{{ $image->alt_text ?: $product->name }}" class="max-h-[90vh] max-w-full object-contain">
                    @endforeach
                </div>
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($product->brand)
                        <a href="{{ route('brands.show', $product->brand->slug) }}" class="text-sm font-semibold uppercase tracking-wide text-ink-muted hover:text-primary-700">{{ $product->brand->name }}</a>
                    @endif
                    <x-pet-badge :species="$product->species" />
                </div>
                <h1 class="mt-3 text-4xl sm:text-5xl">{{ $product->name }}</h1>
                @if ($product->short_description)
                    <p class="mt-4 text-lg text-ink-muted">{{ $product->short_description }}</p>
                @endif
                <div class="mt-5 flex items-center gap-4">
                    <x-rating-stars :rating="$product->rating_avg" :count="$product->rating_count" />
                    <span class="text-sm text-ink-muted">SKU: {{ $product->sku ?: '—' }}</span>
                </div>
                <x-price :product="$product" size="lg" class="mt-6" />
                @if ($recommendation)
                    <div class="mt-5 rounded-2xl {{ $recommendation['status'] === 'warning' ? 'bg-red-50 text-red-900' : 'bg-primary-50 text-primary-900' }} p-4">
                        <p class="font-semibold">{{ $recommendation['status'] === 'warning' ? __('ui.recommendation.warning_for', ['pet' => $currentPet->name]) : __('ui.recommendation.for_pet', ['pet' => $currentPet->name]) }}</p>
                        <ul class="mt-2 list-disc ps-5 text-sm">
                            @foreach ($recommendation['reasons'] as $reason)<li>{{ $reason }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-6 rounded-2xl bg-cream-100 p-4">
                    @if ($product->stock > 0)
                        <p class="font-semibold text-primary-800">{{ __('ui.catalog.in_stock_count', ['count' => $product->stock]) }}</p>
                    @else
                        <p class="font-semibold text-red-700">{{ __('ui.product.out_of_stock') }}</p>
                    @endif
                </div>

                <form method="POST" action="{{ route('cart.items.store') }}" x-data="{ quantity: 1 }" class="mt-6 flex flex-wrap gap-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" x-model="quantity">
                    <div class="flex items-center rounded-xl border border-stroke bg-white">
                        <button type="button" class="h-12 w-12 text-xl" @click="quantity = Math.max(1, quantity - 1)" aria-label="{{ __('ui.catalog.decrease_quantity') }}">−</button>
                        <output class="w-10 text-center font-semibold" x-text="quantity">1</output>
                        <button type="button" class="h-12 w-12 text-xl" @click="quantity = Math.min({{ max(1, $product->stock) }}, quantity + 1)" aria-label="{{ __('ui.catalog.increase_quantity') }}">+</button>
                    </div>
                    <x-button type="submit" variant="accent" size="lg" :disabled="$product->stock < 1">
                        <x-icon name="cart" class="h-5 w-5" /> {{ __('ui.catalog.add_to_cart') }}
                    </x-button>
                </form>

                <div class="mt-10 border-t border-line pt-6">
                    <div class="flex gap-2" x-data="{ tab: 'details' }">
                        <button type="button" class="rounded-full px-4 py-2 text-sm font-semibold" :class="tab === 'details' ? 'bg-primary-100 text-primary-800' : 'text-ink-muted'" @click="tab = 'details'">{{ __('ui.catalog.details') }}</button>
                        <button type="button" class="rounded-full px-4 py-2 text-sm font-semibold" :class="tab === 'reviews' ? 'bg-primary-100 text-primary-800' : 'text-ink-muted'" @click="tab = 'reviews'">{{ __('ui.catalog.reviews') }}</button>
                        <div class="mt-5 w-full" x-show="tab === 'details'">
                            <p class="whitespace-pre-line text-ink-muted">{{ $product->description ?: __('ui.catalog.no_description') }}</p>
                            @if ($product->ingredients)
                                <h2 class="mt-6 text-lg">{{ __('ui.catalog.ingredients') }}</h2>
                                <p class="mt-2 whitespace-pre-line text-sm text-ink-muted">{{ $product->ingredients }}</p>
                            @endif
                        </div>
                        <div class="mt-5 w-full" x-show="tab === 'reviews'" x-cloak>
                            <div class="mb-5 rounded-xl bg-cream-100 p-4">
                                <p class="text-lg font-semibold">{{ number_format((float) ($product->rating_avg ?? 0), 1, ',', '.') }} / 5 ({{ $product->rating_count ?? 0 }})</p>
                                @foreach (range(5, 1) as $stars)
                                    @php($count = (int) ($reviewDistribution[$stars] ?? 0))
                                    <div class="mt-1 flex items-center gap-2 text-xs"><span class="w-8">{{ $stars }} ★</span><div class="h-2 flex-1 rounded-full bg-white"><span class="block h-2 rounded-full bg-accent-500" style="width: {{ ($product->rating_count ?? 0) ? ($count / $product->rating_count * 100) : 0 }}%"></span></div><span class="w-6 text-right">{{ $count }}</span></div>
                                @endforeach
                            </div>
                            @forelse ($reviews as $review)
                                <article class="border-b border-line py-3">
                                    <div class="flex justify-between"><strong>{{ $review->user->name }}</strong><span>{{ str_repeat('★', $review->rating) }}</span></div>
                                    @if ($review->title)<p class="mt-1 font-semibold">{{ $review->title }}</p>@endif
                                    <p class="mt-1 text-sm">{{ $review->body }}</p>
                                    @if ($review->isVerifiedPurchase())<span class="text-xs text-primary-700">{{ __('ui.review.verified') }}</span>@endif
                                </article>
                            @empty
                                <p class="text-ink-muted">{{ __('ui.review.empty') }}</p>
                            @endforelse
                            @auth
                                <form method="POST" action="{{ route('reviews.store', $product) }}" enctype="multipart/form-data" class="mt-5 grid gap-3">@csrf
                                    <x-select name="rating" :label="__('ui.review.rating')" :options="[5 => '5 ★', 4 => '4 ★', 3 => '3 ★', 2 => '2 ★', 1 => '1 ★']" required />
                                    <x-input name="title" :label="__('ui.review.title')" />
                                    <x-textarea name="body" :label="__('ui.review.body')" />
                                    <x-input name="photo" type="file" :label="__('ui.review.photo')" />
                                    <x-button type="submit">{{ __('ui.review.submit') }}</x-button>
                                </form>
                            @endauth
                            <div class="mt-4">{{ $reviews->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <section class="mt-16 border-t border-line pt-12">
                <h2 class="text-2xl">{{ __('ui.catalog.related') }}</h2>
                <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
