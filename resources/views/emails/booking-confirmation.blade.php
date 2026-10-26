@component('mail::message')
# {{ __('ui.booking.email_title') }}

{{ __('ui.booking.email_intro', ['service' => $booking->service->name, 'pet' => $booking->pet->name]) }}

**{{ __('ui.booking.choose_date') }}:** {{ $booking->starts_at->format('d/m/Y H:i') }}  
**{{ __('ui.booking.staff') }}:** {{ $booking->staff->name }}  
**{{ __('ui.booking.status') }}:** {{ $booking->status->label() }}

@component('mail::button', ['url' => route('bookings.show', $booking)])
{{ __('ui.booking.view_booking') }}
@endcomponent

{{ __('ui.booking.email_footer') }}

{{ config('petshop.name') }}
@endcomponent
