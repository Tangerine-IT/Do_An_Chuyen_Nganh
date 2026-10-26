<x-app-layout :title="__('ui.orders.detail')">
    <div class="container-page py-10">
        <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('ui.account.orders') }}</a>
        <div class="mt-4 flex flex-wrap items-end justify-between gap-4"><div><h1 class="text-3xl">#{{ $order->order_number }}</h1><p class="mt-2 text-ink-muted">{{ $order->status->label() }}</p></div>
            @if ($order->status === \App\Enums\OrderStatus::Pending)<form method="POST" action="{{ route('orders.cancel', $order) }}">@csrf<x-button type="submit" variant="danger" onclick="return confirm('{{ __('ui.orders.cancel_confirm') }}')">{{ __('ui.orders.cancel') }}</x-button></form>@endif
        </div>
        <div class="mt-4"><p class="text-sm">{{ __('ui.checkout.payment') }}: <strong>{{ $order->payment_status->label() }}</strong></p></div>
        @if ($order->payment_method === \App\Enums\PaymentMethod::Vnpay && $order->payment_status === \App\Enums\PaymentStatus::Failed)
            <form method="POST" action="{{ route('orders.pay-again', $order) }}" class="mt-3">@csrf<x-button type="submit" size="sm" variant="accent">{{ __('ui.orders.pay_again') }}</x-button></form>
        @endif
        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_22rem]">
            <x-card><h2 class="text-xl">{{ __('ui.orders.items') }}</h2><div class="mt-4 divide-y divide-line">@foreach ($order->items as $item)<div class="flex justify-between gap-4 py-4"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><strong>{{ format_vnd($item->line_total) }}</strong></div>@endforeach</div></x-card>
            <div class="space-y-6"><x-card><h2 class="text-xl">{{ __('ui.orders.shipping') }}</h2><p class="mt-3 text-sm">{{ $order->recipient_name }} · {{ $order->recipient_phone }}</p><p class="mt-2 text-sm text-ink-muted">{{ $order->shipping_address }}, {{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_province }}</p></x-card><x-card><div class="flex justify-between"><span>{{ __('ui.cart.subtotal') }}</span><strong>{{ format_vnd($order->subtotal) }}</strong></div><div class="mt-2 flex justify-between"><span>{{ __('ui.checkout.title') }}</span><strong>{{ format_vnd($order->shipping_fee) }}</strong></div><div class="mt-4 border-t border-line pt-4 flex justify-between text-xl"><span>{{ __('ui.orders.total') }}</span><strong>{{ format_vnd($order->total) }}</strong></div></x-card></div>
        </div>
    </div>
</x-app-layout>
