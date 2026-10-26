<x-app-layout :title="$listing->name.' - '.__('ui.adoption.title')" :description="$listing->description">
    <article class="container-page py-10 sm:py-14">
        <a class="text-sm font-semibold text-primary-700 hover:underline" href="{{ route('adoption.index') }}">← {{ __('ui.adoption.back') }}</a>
        <div class="mt-6 grid gap-8 lg:grid-cols-[1.1fr_1fr]">
            <div>
                @if($listing->photo_path)<img class="aspect-[4/3] w-full rounded-2xl object-cover" src="{{ storage_url($listing->photo_path) }}" alt="{{ $listing->name }}">@endif
                @if($listing->gallery_paths)<div class="mt-3 grid grid-cols-3 gap-3">@foreach($listing->gallery_paths as $image)<img class="aspect-square rounded-xl object-cover" src="{{ storage_url($image) }}" alt="">@endforeach</div>@endif
            </div>
            <div><p class="text-sm font-semibold text-primary-700">{{ $listing->species->label() }} · {{ $listing->city }}</p><h1 class="mt-2 text-4xl">{{ $listing->name }}</h1><p class="mt-4 text-ink-muted">{{ $listing->description }}</p><dl class="mt-6 grid grid-cols-2 gap-4 text-sm"><div><dt class="text-ink-muted">{{ __('ui.adoption.age') }}</dt><dd class="font-semibold">{{ $listing->age_months }} {{ __('ui.adoption.months') }}</dd></div><div><dt class="text-ink-muted">{{ __('ui.adoption.health') }}</dt><dd class="font-semibold">{{ $listing->is_neutered ? __('ui.adoption.neutered') : __('ui.adoption.not_neutered') }}</dd></div><div><dt class="text-ink-muted">{{ __('ui.adoption.vaccination') }}</dt><dd class="font-semibold">{{ $listing->is_vaccinated ? __('ui.adoption.vaccinated') : __('ui.adoption.not_vaccinated') }}</dd></div></dl>@if($listing->health_notes)<p class="mt-5"><strong>{{ __('ui.adoption.health') }}:</strong> {{ $listing->health_notes }}</p>@endif @if($listing->vaccination_notes)<p class="mt-2"><strong>{{ __('ui.adoption.vaccination') }}:</strong> {{ $listing->vaccination_notes }}</p>@endif
                @auth
                    <x-card class="mt-8"><h2 class="text-xl">{{ __('ui.adoption.apply') }}</h2><form method="POST" action="{{ route('adoption.apply', $listing) }}" class="mt-4 space-y-4">@csrf<x-input name="phone" :label="__('ui.adoption.phone')" :value="auth()->user()->phone" required /><div><label class="form-label" for="message">{{ __('ui.adoption.message') }}</label><textarea class="form-control" id="message" name="message" rows="4" required></textarea></div><x-button type="submit">{{ __('ui.adoption.apply_submit') }}</x-button></form></x-card>
                @else
                    <x-button class="mt-8" :href="route('login')">{{ __('ui.adoption.login_to_apply') }}</x-button>
                @endauth
            </div>
        </div>
    </article>
</x-app-layout>
