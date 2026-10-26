<x-app-layout :title="__('ui.home.title')" :description="__('ui.home.description')">
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-cream-200 to-cream-100">
        <div class="pointer-events-none absolute -start-24 top-10 h-72 w-72 rounded-full bg-primary-100/70 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -end-20 bottom-0 h-72 w-72 rounded-full bg-accent-100/80 blur-3xl" aria-hidden="true"></div>

        <div class="container-page relative grid items-center gap-10 py-12 sm:py-16 lg:grid-cols-2 lg:gap-14 lg:py-20">
            <div>
                <p class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-primary-700 shadow-soft">
                    <x-icon name="paw" class="h-4 w-4 text-accent-600" />
                    {{ __('ui.home.hero.eyebrow') }}
                </p>

                <h1 class="mt-5 text-4xl leading-[1.1] sm:text-5xl lg:text-6xl">{{ __('ui.home.hero.title') }}</h1>
                <p class="mt-5 max-w-xl text-lg text-ink-muted">{{ __('ui.home.hero.text') }}</p>

                <form action="{{ route('products.index') }}" method="GET" role="search" class="mt-8 max-w-xl">
                    <label for="hero-search" class="sr-only">{{ __('ui.search.label') }}</label>
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute start-5 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-muted" />
                        <input id="hero-search" type="search" name="q" placeholder="{{ __('ui.search.placeholder') }}" class="form-control rounded-full bg-white py-4 ps-12 pe-32 text-base shadow-soft">
                        <button type="submit" class="absolute end-2 top-1/2 -translate-y-1/2 rounded-full bg-accent-500 px-6 py-2.5 text-sm font-semibold text-primary-950 transition hover:bg-accent-400">
                            {{ __('ui.search.submit') }}
                        </button>
                    </div>
                </form>

                <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                    <span class="text-ink-muted">{{ __('ui.home.hero.popular') }}</span>
                    @foreach ([['dog', 'chip_dog'], ['cat', 'chip_cat'], ['fish', 'chip_fish']] as [$species, $label])
                        <a href="{{ route('products.index', ['species' => $species]) }}" class="rounded-full bg-white px-3.5 py-1.5 font-semibold text-primary-800 ring-1 ring-line transition hover:bg-primary-50">
                            {{ __('ui.home.hero.'.$label) }}
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <x-button :href="route('products.index')" variant="accent" size="lg">{{ __('ui.home.hero.primary_cta') }}</x-button>
                    <x-button :href="route('services.index')" variant="secondary" size="lg">{{ __('ui.home.hero.secondary_cta') }}</x-button>
                </div>
            </div>

            {{-- Hero art. Swap for real pet photography by replacing the inner card with an <img>. --}}
            <div class="relative mx-auto aspect-square w-full max-w-sm sm:max-w-md lg:max-w-lg" aria-hidden="true">
                <div class="absolute inset-3 rotate-3 rounded-[3rem] bg-primary-700"></div>
                <div class="absolute inset-3 -rotate-2 overflow-hidden rounded-[3rem] bg-gradient-to-br from-accent-100 via-cream-50 to-primary-100 shadow-lift">
                    <x-icon name="paw" class="absolute -end-6 -top-6 h-40 w-40 rotate-12 text-white/70" />
                    <span class="absolute start-[10%] top-[22%] text-[7.5rem] leading-none sm:text-[9rem]">🐶</span>
                    <span class="absolute bottom-[10%] end-[8%] text-[6rem] leading-none sm:text-[7.5rem]">🐱</span>
                </div>

                <div class="absolute -start-1 top-8 flex items-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-primary-900 shadow-lift sm:-start-4">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-800"><x-icon name="warning" class="h-5 w-5" /></span>
                    {{ __('ui.home.hero.badge_safe') }}
                </div>
                <div class="absolute -end-1 bottom-24 flex items-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-primary-900 shadow-lift sm:-end-4">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-700"><x-icon name="calendar" class="h-5 w-5" /></span>
                    {{ __('ui.home.hero.badge_reminder') }}
                </div>
            </div>
        </div>

        @guest
            <div class="container-page relative pb-10">
                <x-pet-profile-cta class="mx-auto max-w-3xl" />
            </div>
        @endguest
    </section>

    {{-- Categories --}}
    <section class="container-page pt-14 sm:pt-16">
        <x-section-heading :title="__('ui.home.categories.title')" :subtitle="__('ui.home.categories.subtitle')" :href="route('products.index')" />

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-6">
            @foreach ($categories as $category)
                <x-category-tile :category="$category" />
            @endforeach
        </div>
    </section>

    {{-- Featured products --}}
    <section class="container-page pt-14 sm:pt-16">
        <x-section-heading :title="__('ui.home.featured.title')" :subtitle="__('ui.home.featured.subtitle')" :href="route('products.index')" />

        @if ($featuredProducts->isEmpty())
            <x-empty-state :title="__('ui.home.featured.empty_title')" :description="__('ui.home.featured.empty_text')" icon="bag" />
        @else
            <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @foreach ($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </section>

    @if ($currentPet && ! $currentPet->hasCompleteRecommendationProfile())
        <section class="container-page pt-14 sm:pt-16">
            <x-pet-profile-cta :pet="$currentPet" />
        </section>
    @elseif ($currentPet && $recommendedProducts->isNotEmpty())
        <section class="container-page pt-14 sm:pt-16">
            <x-section-heading :title="__('ui.recommendation.home_title', ['pet' => $currentPet->name])" :subtitle="__('ui.recommendation.home_subtitle')" :href="route('products.index', ['for_pet' => 1])" />
            <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @foreach ($recommendedProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Services --}}
    @if ($services->isNotEmpty())
        <section class="mt-14 bg-cream-200 py-14 sm:mt-16 sm:py-16">
            <div class="container-page">
                <x-section-heading :title="__('ui.home.services.title')" :subtitle="__('ui.home.services.subtitle')" :href="route('services.index')" />

                <div class="grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-4">
                    @foreach ($services as $service)
                        <x-service-card :service="$service" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Why us --}}
    <section class="container-page pt-14 sm:pt-16">
        <x-section-heading :title="__('ui.home.why.title')" :subtitle="__('ui.home.why.subtitle')" />

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([['paw', 'profile'], ['warning', 'allergy'], ['calendar', 'health'], ['bag', 'delivery']] as [$icon, $key])
                <x-card class="h-full">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-100 text-primary-700">
                        <x-icon :name="$icon" class="h-6 w-6" />
                    </span>
                    <h3 class="mt-4 text-lg">{{ __('ui.home.why.'.$key.'_title') }}</h3>
                    <p class="mt-1.5 text-sm text-ink-muted">{{ __('ui.home.why.'.$key.'_text') }}</p>
                </x-card>
            @endforeach
        </div>
    </section>

    {{-- Blog --}}
    @if ($posts->isNotEmpty())
        <section class="container-page pt-14 sm:pt-16">
            <x-section-heading :title="__('ui.home.blog.title')" :subtitle="__('ui.home.blog.subtitle')" :href="route('blog.index')" />

            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Newsletter --}}
    <section id="newsletter" class="container-page pt-14 sm:pt-16">
        <div class="relative overflow-hidden rounded-3xl bg-primary-800 px-6 py-10 text-white shadow-lift sm:px-12 sm:py-12">
            <x-icon name="paw" class="pointer-events-none absolute -end-8 -top-8 h-48 w-48 rotate-12 text-primary-700/70" />

            <div class="relative grid items-center gap-6 lg:grid-cols-2">
                <div>
                    <h2 class="text-2xl text-white sm:text-3xl">{{ __('ui.newsletter.title') }}</h2>
                    <p class="mt-2 text-primary-100">{{ __('ui.newsletter.text') }}</p>
                </div>

                <form method="POST" action="{{ route('newsletter.subscribe') }}" class="flex w-full flex-col gap-3 sm:flex-row" novalidate>
                    @csrf
                    <div class="flex-1">
                        <label for="newsletter-email" class="sr-only">{{ __('ui.newsletter.label') }}</label>
                        <input
                            id="newsletter-email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="{{ __('ui.newsletter.placeholder') }}"
                            class="form-control rounded-full py-3"
                            @error('email') aria-invalid="true" aria-describedby="newsletter-error" @enderror
                        >
                        @error('email')
                            <p id="newsletter-error" class="mt-2 ps-4 text-sm font-medium text-red-100">{{ $message }}</p>
                        @enderror
                    </div>
                    <x-button type="submit" variant="accent" size="lg" class="sm:self-start">{{ __('ui.newsletter.submit') }}</x-button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
