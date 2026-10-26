<x-app-layout :title="__('ui.account.pets')">
    <div class="container-page grid gap-8 py-10 lg:grid-cols-[15rem_1fr]">
        @include('account.partials.sidebar', ['active' => 'pets'])
        <section>
            <h1 class="text-3xl">{{ __('ui.account.pets') }}</h1>
            <p class="mt-2 text-ink-muted">{{ __('ui.account.pets_intro') }}</p>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @forelse ($pets as $pet)
                    <x-card class="flex flex-col justify-between gap-5">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-xl font-semibold">{{ $pet->name }}</h2>
                                    <p class="mt-1 text-sm text-ink-muted">{{ $pet->species->label() }} · {{ $pet->sex->label() }}</p>
                                    @if ($pet->lifeStage())
                                        <p class="mt-1 text-xs text-ink-muted">{{ $pet->lifeStage()->label() }} · {{ __('ui.account.pet_age_months', ['count' => $pet->ageInMonths()]) }}</p>
                                    @endif
                                </div>
                                @if ($pet->breed)
                                    <x-badge>{{ $pet->breed }}</x-badge>
                                @endif
                            </div>
                            <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
                                @if ($pet->birthday)
                                    <div><dt class="text-ink-muted">{{ __('ui.account.pet_birthday') }}</dt><dd>{{ $pet->birthday->format('d/m/Y') }}</dd></div>
                                @endif
                                @if ($pet->weight_kg)
                                    <div><dt class="text-ink-muted">{{ __('ui.account.pet_weight') }}</dt><dd>{{ $pet->weight_kg }} kg</dd></div>
                                @endif
                            </dl>
                            @if ($pet->allergies)
                                <p class="mt-3 text-sm"><span class="font-semibold">{{ __('ui.account.pet_allergies') }}:</span> {{ implode(', ', $pet->allergies) }}</p>
                            @endif
                            @if ($pet->notes)
                                <p class="mt-2 text-sm text-ink-muted">{{ $pet->notes }}</p>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-button :href="route('account.pets.show', $pet)" variant="ghost" size="sm">{{ __('ui.account.health_book') }}</x-button>
                            <form method="POST" action="{{ route('account.pets.current', $pet) }}">@csrf<x-button type="submit" variant="secondary" size="sm">{{ $currentPet?->is($pet) ? __('ui.account.current_pet_selected') : __('ui.account.select_current_pet') }}</x-button></form>
                            <form method="POST" action="{{ route('account.pets.destroy', $pet) }}">@csrf @method('DELETE')<x-button type="submit" variant="danger" size="sm">{{ __('ui.common.delete') }}</x-button></form>
                        </div>
                    </x-card>
                @empty
                    <x-empty-state class="md:col-span-2" :title="__('ui.account.no_pets')" />
                @endforelse
            </div>

            <x-card class="mt-8">
                <h2 class="text-xl">{{ __('ui.account.add_pet') }}</h2>
                <form method="POST" action="{{ route('account.pets.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-2">@csrf
                    <x-input name="name" :label="__('ui.account.pet_name')" required />
                    <x-select name="species" :label="__('ui.account.pet_species')" :options="$species" required />
                    <div>
                        <x-input name="breed" list="pet-breeds" :label="__('ui.account.pet_breed')" />
                        <datalist id="pet-breeds">
                            @foreach (collect($breeds)->flatten()->unique() as $breed)<option value="{{ $breed }}">@endforeach
                        </datalist>
                    </div>
                    <x-select name="sex" :label="__('ui.account.pet_sex')" :options="$sexes" required />
                    <x-input name="birthday" type="date" :label="__('ui.account.pet_birthday')" />
                    <x-input name="weight_kg" type="number" step="0.01" min="0" :label="__('ui.account.pet_weight')" />
                    <x-input name="photo" type="file" accept="image/*" :label="__('ui.account.pet_photo')" />
                    <x-input name="allergies" :label="__('ui.account.pet_allergies')" :hint="__('ui.account.pet_allergies_hint')" wrapper-class="sm:col-span-2" />
                    <x-textarea name="notes" :label="__('ui.account.pet_notes')" wrapper-class="sm:col-span-2" />
                    <div><x-button type="submit">{{ __('ui.common.save') }}</x-button></div>
                </form>
            </x-card>
        </section>
    </div>
</x-app-layout>
