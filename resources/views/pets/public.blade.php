<x-app-layout :title="__('ui.account.public_pet_title')">
    <div class="container-page max-w-2xl py-12">
        <x-card>
            <div class="flex flex-wrap items-center gap-5">
                @if ($pet->public_photo && $pet->photo_path)
                    <img src="{{ storage_url($pet->photo_path) }}" alt="{{ $pet->public_name ? $pet->name : __('ui.account.public_pet') }}" class="h-24 w-24 rounded-2xl object-cover">
                @else
                    <span class="flex h-24 w-24 items-center justify-center rounded-2xl bg-primary-100 text-primary-700"><x-icon name="paw" class="h-10 w-10" /></span>
                @endif
                <div>
                    <p class="text-sm font-semibold text-primary-700">{{ __('ui.account.public_pet_badge') }}</p>
                    @if ($pet->public_name)<h1 class="mt-1 text-3xl">{{ $pet->name }}</h1>@else<h1 class="mt-1 text-3xl">{{ __('ui.account.public_pet') }}</h1>@endif
                    <p class="mt-1 text-ink-muted">{{ $pet->species->label() }} · {{ $pet->sex->label() }}</p>
                </div>
            </div>
            @if ($pet->public_vaccinations)
                <div class="mt-8 rounded-xl bg-emerald-50 p-4 text-emerald-950">
                    <p class="font-semibold">{{ __('ui.account.vaccination_status') }}</p>
                    <p class="mt-1 text-sm">{{ trans_choice('ui.account.vaccination_count', $vaccinationCount, ['count' => $vaccinationCount]) }}</p>
                </div>
            @endif
            @if ($pet->public_contact)
                <div class="mt-4 rounded-xl bg-cream-100 p-4">
                    <p class="font-semibold">{{ __('ui.account.owner_contact') }}</p>
                    <p class="mt-1 text-sm">{{ $pet->user->name }} · {{ $pet->user->phone ?: $pet->user->email }}</p>
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
