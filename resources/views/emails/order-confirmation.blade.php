@component('mail::message')
# {{ config('petshop.name') }}

{{ __('mail.order_greeting', ['name' => $order->recipient_name]) }}

{{ __('mail.order_intro', ['order' => $order->order_number]) }}

@component('mail::table')
| {{ __('mail.order_product') }} | {{ __('mail.order_quantity') }} | {{ __('mail.order_total') }} |
|:---|:---:|---:|
@foreach ($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | {{ format_vnd($item->line_total) }} |
@endforeach
@endcomponent

**{{ __('mail.order_grand_total') }}:** {{ format_vnd($order->total) }}

@component('mail::button', ['url' => route('orders.show', $order)])
{{ __('mail.order_view') }}
@endcomponent

{{ __('mail.order_footer') }}
@endcomponent
