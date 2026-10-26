<x-app-layout :title="__('ui.account.bookings')">
    <div class="container-page grid gap-8 py-10 lg:grid-cols-[15rem_1fr]">
        @include('account.partials.sidebar', ['active' => 'bookings'])
        <section><h1 class="text-3xl">{{ __('ui.account.bookings') }}</h1><div class="mt-6 space-y-3">
            @forelse($bookings as $booking)<a href="{{ route('bookings.show', $booking) }}" class="block rounded-2xl bg-white p-5 shadow-soft ring-1 ring-line hover:ring-primary-300"><div class="flex justify-between gap-3"><strong>{{ $booking->service->name }}</strong><span>{{ $booking->status->label() }}</span></div><p class="mt-2 text-sm text-ink-muted">{{ $booking->pet->name }} · {{ $booking->starts_at->format('d/m/Y H:i') }} · {{ $booking->staff->name }}</p></a>@empty<x-empty-state :title="__('ui.account.bookings_empty')" />@endforelse
        </div><div class="mt-6">{{ $bookings->links() }}</div></section>
    </div>
</x-app-layout>
