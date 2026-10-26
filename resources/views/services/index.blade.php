<x-app-layout :title="__('ui.booking.services_title')">
    <div class="container-page py-10 sm:py-14">
        <p class="eyebrow">{{ __('ui.nav.services') }}</p>
        <h1 class="mt-2 text-4xl">{{ __('ui.booking.services_title') }}</h1>
        <p class="mt-3 max-w-2xl text-ink-muted">{{ __('ui.booking.services_description') }}</p>
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($services as $service)
                <x-card class="flex flex-col">
                    <h2 class="text-xl">{{ $service->name }}</h2>
                    <p class="mt-2 flex-1 text-sm text-ink-muted">{{ $service->description }}</p>
                    <div class="mt-5 flex items-center justify-between text-sm"><span>{{ $service->duration_label }}</span><strong>{{ format_vnd($service->price) }}</strong></div>
                    <x-button :href="route('services.show', array_filter(['service' => $service->slug, 'pet' => request('pet')]))" class="mt-5 w-full">{{ __('ui.booking.book_now') }}</x-button>
                </x-card>
            @empty
                <x-empty-state :title="__('ui.coming_soon.title')" />
            @endforelse
        </div>
    </div>
</x-app-layout>
