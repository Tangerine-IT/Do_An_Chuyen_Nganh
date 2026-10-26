<x-app-layout :title="__('ui.booking.appointment')">
    <div class="container-page max-w-3xl py-10"><a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('ui.account.bookings') }}</a><h1 class="mt-4 text-3xl">{{ $booking->service->name }}</h1><x-card class="mt-6 space-y-3"><p><strong>{{ __('ui.booking.choose_pet') }}:</strong> {{ $booking->pet->name }}</p><p><strong>{{ __('ui.booking.staff') }}:</strong> {{ $booking->staff->name }}</p><p><strong>{{ __('ui.booking.choose_date') }}:</strong> {{ $booking->starts_at->format('d/m/Y H:i') }} - {{ $booking->ends_at->format('H:i') }}</p><p><strong>{{ __('ui.booking.status') }}:</strong> {{ $booking->status->label() }}</p><p><strong>{{ __('ui.booking.price') }}:</strong> {{ format_vnd($booking->price) }}</p></x-card>
        @if (in_array($booking->status, [\App\Enums\BookingStatus::Pending, \App\Enums\BookingStatus::Confirmed], true))<form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-6">@csrf<x-button type="submit" variant="danger" onclick="return confirm('{{ __('ui.booking.cancel_confirm') }}')">{{ __('ui.booking.cancel') }}</x-button></form>@endif
        @if ($booking->starts_at->greaterThan(now()->addHours((int) config('petshop.booking.cancel_before_hours'))) && in_array($booking->status, [\App\Enums\BookingStatus::Pending, \App\Enums\BookingStatus::Confirmed], true))
            <form method="POST" action="{{ route('bookings.reschedule', $booking) }}" class="mt-6 grid gap-3 sm:grid-cols-[1fr_auto]">@csrf @method('PATCH')
                <x-input name="starts_at" type="datetime-local" :label="__('ui.booking.reschedule')" />
                <x-button type="submit" variant="secondary" class="self-end">{{ __('ui.booking.reschedule') }}</x-button>
            </form>
        @endif
    </div>
</x-app-layout>
