<x-app-layout :title="__('ui.adoption.title')" :description="__('ui.adoption.subtitle')">
    <div class="container-page py-10 sm:py-14">
        <h1 class="text-4xl">{{ __('ui.adoption.title') }}</h1>
        <p class="mt-3 max-w-2xl text-ink-muted">{{ __('ui.adoption.subtitle') }}</p>
        <form method="GET" class="mt-8 grid gap-3 sm:grid-cols-4">
            <x-select name="species" :label="__('ui.adoption.species')" :options="['' => __('ui.adoption.all'), 'dog' => __('enums.pet_species.dog'), 'cat' => __('enums.pet_species.cat'), 'bird' => __('enums.pet_species.bird'), 'other' => __('ui.adoption.other')]" :value="request('species')" />
            <x-select name="age" :label="__('ui.adoption.age')" :options="['' => __('ui.adoption.all'), 'young' => __('ui.adoption.young'), 'adult' => __('ui.adoption.adult'), 'senior' => __('ui.adoption.senior')]" :value="request('age')" />
            <x-input name="city" :label="__('ui.adoption.city')" :value="request('city')" />
            <x-button type="submit" class="self-end">{{ __('ui.search.submit') }}</x-button>
        </form>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($listings as $listing)
                <article class="overflow-hidden rounded-2xl bg-white shadow-soft ring-1 ring-line">
                    @if($listing->photo_path)<img class="aspect-[4/3] w-full object-cover" src="{{ storage_url($listing->photo_path) }}" alt="{{ $listing->name }}">@endif
                    <div class="p-5"><p class="text-sm text-ink-muted">{{ $listing->species->label() }} · {{ $listing->city }}</p><h2 class="mt-1 text-2xl">{{ $listing->name }}</h2><p class="mt-2 text-sm text-ink-muted">{{ $listing->breed }} · {{ $listing->age_months }} {{ __('ui.adoption.months') }}</p><a class="mt-4 inline-block font-semibold text-primary-700 hover:underline" href="{{ route('adoption.show', $listing) }}">{{ __('ui.adoption.view') }} →</a></div>
                </article>
            @empty
                <x-empty-state class="sm:col-span-2 lg:col-span-3" :title="__('ui.adoption.empty')" />
            @endforelse
        </div>
        <div class="mt-8">{{ $listings->links() }}</div>
    </div>
</x-app-layout>
