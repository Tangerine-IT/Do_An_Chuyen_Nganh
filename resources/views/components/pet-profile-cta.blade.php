@props(['pet' => null])

<div {{ $attributes->class('flex flex-wrap items-center gap-4 rounded-2xl bg-white/90 p-4 shadow-lift ring-1 ring-line backdrop-blur') }}>
    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-accent-100 text-accent-700">
        <x-icon name="paw" class="h-6 w-6" />
    </span>
    <div class="min-w-0 flex-1 basis-48">
        <p class="font-heading font-extrabold text-primary-900">{{ $pet ? __('ui.recommendation.incomplete_title', ['pet' => $pet->name]) : __('ui.pet_cta.title') }}</p>
        <p class="text-sm text-ink-muted">{{ $pet ? __('ui.recommendation.incomplete') : __('ui.pet_cta.text') }}</p>
    </div>
    <x-button :href="$pet ? route('account.pets') : route('register')" size="sm" variant="accent">{{ $pet ? __('ui.account.pets') : __('ui.pet_cta.button') }}</x-button>
</div>
