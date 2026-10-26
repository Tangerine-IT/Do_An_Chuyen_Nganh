<div>
    <div class="grid gap-8 lg:grid-cols-[16rem_1fr]">
        <aside>
            <button type="button" class="mb-4 inline-flex items-center gap-2 text-sm font-semibold text-primary-700 lg:hidden" x-data @click="$dispatch('toggle-catalog-filters')">
                <x-icon name="filter" class="h-5 w-5" /> {{ __('ui.catalog.filters') }}
            </button>

            <form
                class="space-y-6 rounded-2xl bg-white p-5 shadow-soft ring-1 ring-line"
                x-data="{ open: true }"
                @toggle-catalog-filters.window="open = ! open"
                x-show="open"
            >
                <div>
                    <label for="catalog-search" class="text-sm font-semibold text-primary-900">{{ __('ui.catalog.search_label') }}</label>
                    <input wire:model.live.debounce.300ms="search" id="catalog-search" type="search" class="form-control mt-2" placeholder="{{ __('ui.search.placeholder') }}">
                </div>

                <div>
                    <label for="catalog-category" class="text-sm font-semibold text-primary-900">{{ __('ui.catalog.category') }}</label>
                    <select wire:model.live="categoryId" id="catalog-category" class="form-control mt-2">
                        <option value="">{{ __('ui.catalog.all_categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @foreach ($category->children as $child)
                                <option value="{{ $child->id }}">— {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="catalog-brand" class="text-sm font-semibold text-primary-900">{{ __('ui.catalog.brand') }}</label>
                    <select wire:model.live="brandId" id="catalog-brand" class="form-control mt-2">
                        <option value="">{{ __('ui.catalog.all_brands') }}</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="catalog-species" class="text-sm font-semibold text-primary-900">{{ __('ui.catalog.species') }}</label>
                    <select wire:model.live="species" id="catalog-species" class="form-control mt-2">
                        <option value="">{{ __('ui.catalog.all_species') }}</option>
                        @foreach ($speciesOptions as $option)
                            <option value="{{ $option->value }}">{{ $option->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="catalog-stage" class="text-sm font-semibold text-primary-900">{{ __('ui.catalog.life_stage') }}</label>
                    <select wire:model.live="lifeStage" id="catalog-stage" class="form-control mt-2">
                        <option value="">{{ __('ui.catalog.all_life_stages') }}</option>
                        @foreach ($lifeStageOptions as $option)
                            <option value="{{ $option->value }}">{{ $option->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="catalog-min-price" class="text-xs font-semibold text-primary-900">{{ __('ui.catalog.min_price') }}</label>
                        <input wire:model.live.debounce.500ms="minPrice" id="catalog-min-price" type="number" min="0" class="form-control mt-2" placeholder="0">
                    </div>
                    <div>
                        <label for="catalog-max-price" class="text-xs font-semibold text-primary-900">{{ __('ui.catalog.max_price') }}</label>
                        <input wire:model.live.debounce.500ms="maxPrice" id="catalog-max-price" type="number" min="0" class="form-control mt-2" placeholder="∞">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input wire:model.live="onSale" type="checkbox" class="rounded border-stroke text-primary-700 focus:ring-primary-600">
                    {{ __('ui.catalog.on_sale') }}
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input wire:model.live="inStock" type="checkbox" class="rounded border-stroke text-primary-700 focus:ring-primary-600">
                    {{ __('ui.catalog.in_stock') }}
                </label>
                @if ($currentPet)
                    <label class="flex items-center gap-2 text-sm">
                        <input wire:model.live="forPet" type="checkbox" class="rounded border-stroke text-primary-700 focus:ring-primary-600">
                        {{ __('ui.recommendation.for_pet', ['pet' => $currentPet->name]) }}
                    </label>
                    @if ($forPet)
                        <label class="flex items-center gap-2 text-sm">
                            <input wire:model.live="safeOnly" type="checkbox" class="rounded border-stroke text-primary-700 focus:ring-primary-600">
                            {{ __('ui.recommendation.safe_only') }}
                        </label>
                    @endif
                @endif

                <button type="button" wire:click="clearFilters" class="text-sm font-semibold text-primary-700 hover:text-primary-900">
                    {{ __('ui.catalog.clear_filters') }}
                </button>
            </form>
        </aside>

        <section aria-live="polite" aria-busy="false">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-ink-muted">{{ trans_choice('ui.catalog.result_count', $products->total(), ['count' => $products->total()]) }}</p>
                <label class="flex items-center gap-2 text-sm">
                    <span class="sr-only">{{ __('ui.catalog.sort') }}</span>
                    <select wire:model.live="sort" class="form-control py-2">
                        <option value="newest">{{ __('ui.catalog.sort_newest') }}</option>
                        <option value="price_asc">{{ __('ui.catalog.sort_price_asc') }}</option>
                        <option value="price_desc">{{ __('ui.catalog.sort_price_desc') }}</option>
                        <option value="best_selling">{{ __('ui.catalog.sort_best_selling') }}</option>
                    </select>
                </label>
            </div>

            <div wire:loading.delay class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3">
                @for ($i = 0; $i < 6; $i++)
                    <x-product-card-skeleton />
                @endfor
            </div>

            <div wire:loading.remove>
                @if ($products->isEmpty())
                    <x-empty-state :title="__('ui.catalog.empty_title')" :description="__('ui.catalog.empty_description')" icon="search">
                        <x-button type="button" variant="secondary" wire:click="clearFilters">{{ __('ui.catalog.clear_filters') }}</x-button>
                    </x-empty-state>
                @else
                    <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" wire:key="product-{{ $product->id }}" />
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $products->links() }}</div>
                @endif
            </div>
        </section>
    </div>
</div>
