<x-app-layout :title="__('ui.account.title')">
    <div class="container-page grid gap-8 py-10 lg:grid-cols-[15rem_1fr]">
        @include('account.partials.sidebar', ['active' => 'overview'])
        <section>
            <h1 class="text-3xl">{{ __('ui.account.welcome', ['name' => $user->name]) }}</h1>
            <p class="mt-2 text-ink-muted">{{ __('ui.account.overview_text') }}</p>
            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                <x-card><p class="text-sm text-ink-muted">{{ __('ui.account.addresses') }}</p><p class="mt-1 text-2xl font-bold">{{ $user->addresses()->count() }}</p></x-card>
                <x-card><p class="text-sm text-ink-muted">{{ __('ui.account.orders') }}</p><p class="mt-1 text-2xl font-bold">{{ $user->orders()->count() }}</p></x-card>
                <x-card><p class="text-sm text-ink-muted">{{ __('ui.account.pets') }}</p><p class="mt-1 text-2xl font-bold">{{ $user->pets()->count() }}</p></x-card>
            </div>
            @if ($currentPet && ! $currentPet->hasCompleteRecommendationProfile())
                <div class="mt-8 rounded-2xl bg-amber-50 p-5 text-amber-950 ring-1 ring-amber-200">
                    <h2 class="font-semibold">{{ __('ui.recommendation.incomplete_title', ['pet' => $currentPet->name]) }}</h2>
                    <p class="mt-1 text-sm">{{ __('ui.recommendation.incomplete') }}</p>
                    <x-button :href="route('account.pets')" variant="secondary" size="sm" class="mt-4">{{ __('ui.account.pets') }}</x-button>
                </div>
            @elseif ($currentPet && $recommendedProducts->isNotEmpty())
                <section class="mt-8">
                    <h2 class="text-2xl">{{ __('ui.recommendation.home_title', ['pet' => $currentPet->name]) }}</h2>
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        @foreach ($recommendedProducts as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                </section>
            @endif
            @if ($currentPet && $upcomingHealthRecords->isNotEmpty())
                <section class="mt-8 rounded-2xl bg-cream-100 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-2xl">{{ __('ui.account.health_upcoming') }}</h2>
                        <a href="{{ route('account.pets.show', $currentPet) }}" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('ui.common.view_details') }}</a>
                    </div>
                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach ($upcomingHealthRecords as $record)
                            <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 text-sm">
                                <span>{{ $record->type->label() }}</span>
                                <span class="{{ $record->status() === 'overdue' ? 'text-red-700' : ($record->status() === 'due_soon' ? 'text-amber-700' : 'text-emerald-700') }}">{{ $record->next_due_date->format('d/m/Y') }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </section>
    </div>
</x-app-layout>
