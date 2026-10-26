<x-app-layout :title="$service->name">
    <div class="container-page max-w-4xl py-10 sm:py-14">
        <a href="{{ route('services.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('ui.booking.services_title') }}</a>
        <h1 class="mt-4 text-4xl">{{ $service->name }}</h1>
        <p class="mt-4 text-lg text-ink-muted">{{ $service->description }}</p>
        <div class="mt-6 flex flex-wrap gap-4"><x-badge variant="primary">{{ $service->duration_label }}</x-badge><x-badge>{{ format_vnd($service->price) }}</x-badge></div>
        <x-button :href="route('booking.create', array_filter(['service' => $service->id, 'pet' => request('pet')]))" class="mt-8">{{ __('ui.booking.book_now') }}</x-button>
    </div>
</x-app-layout>
