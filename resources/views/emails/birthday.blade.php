@component('mail::message')
# {{ config('petshop.name') }}

{{ __('mail.birthday_greeting', ['pet' => $pet->name]) }}

{{ __('mail.birthday_line', ['code' => $coupon->code]) }}

@component('mail::panel')
{{ __('mail.birthday_code_label') }}: **{{ $coupon->code }}**
@endcomponent

{{ config('petshop.name') }}
@endcomponent
