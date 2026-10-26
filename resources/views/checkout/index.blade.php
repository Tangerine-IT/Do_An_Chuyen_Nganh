<x-app-layout :title="__('ui.checkout.title')">
    <div class="container-page py-10 sm:py-14">
        <h1 class="text-3xl">{{ __('ui.checkout.title') }}</h1>
        @if ($addresses->isEmpty())
            <x-card class="mt-8">
                <p>{{ __('ui.checkout.no_address') }}</p>
                <x-button :href="route('account.addresses')" class="mt-4">{{ __('ui.checkout.add_address') }}</x-button>
            </x-card>
        @else
            <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-[1fr_22rem]">@csrf
                <div class="space-y-6">
                    <x-card>
                        <h2 class="text-xl">{{ __('ui.checkout.address') }}</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($addresses as $address)
                                <label class="flex cursor-pointer gap-3 rounded-xl border border-line p-4 has-[:checked]:border-primary-700 has-[:checked]:bg-primary-50">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" @checked(old('address_id', $address->is_default ? $address->id : null) == $address->id) required>
                                    <span><strong>{{ $address->label }}</strong> · {{ $address->recipient_name }} · {{ $address->phone }}<br><span class="text-sm text-ink-muted">{{ $address->address_line }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->province }}</span></span>
                                </label>
                            @endforeach
                        </div>
                    </x-card>
                    <x-card>
                        <h2 class="text-xl">{{ __('ui.checkout.payment') }}</h2>
                        <label class="mt-4 flex items-center gap-3"><input type="radio" name="payment_method" value="cod" checked> {{ __('ui.checkout.cod') }}</label>
                        <label class="mt-3 flex items-center gap-3"><input type="radio" name="payment_method" value="vnpay" @checked(old('payment_method') === 'vnpay')> VNPay Sandbox</label>
                        <label class="mt-3 flex items-center gap-3 text-ink-muted"><input type="radio" value="momo" disabled> MoMo <span class="text-xs">({{ __('ui.checkout.online_coming_soon') }})</span></label>
                        <x-input name="coupon_code" :label="__('ui.checkout.coupon')" class="mt-5" />
                        @if(auth()->user()->loyalty_points > 0)
                            <x-input type="number" name="loyalty_points" :label="__('ui.loyalty.use_points')" min="0" :max="auth()->user()->loyalty_points" class="mt-5" />
                        @endif
                        <x-textarea name="note" :label="__('ui.checkout.note')" class="mt-5" />
                    </x-card>
                </div>
                @php($subtotal = $cart->items->sum(fn ($item) => $item->product->current_price * $item->quantity))
                @php($shipping = $subtotal >= config('petshop.shipping.free_threshold') ? 0 : config('petshop.shipping.fee'))
                <x-card class="h-fit">
                    <h2 class="text-xl">{{ __('ui.cart.subtotal') }}</h2>
                    @if ($errors->has('checkout'))<p class="mt-3 text-sm text-red-700">{{ $errors->first('checkout') }}</p>@endif
                    <dl class="mt-4 space-y-2 text-sm"><div class="flex justify-between"><dt>{{ __('ui.cart.subtotal') }}</dt><dd>{{ format_vnd($subtotal) }}</dd></div><div class="flex justify-between"><dt>{{ __('ui.checkout.title') }}</dt><dd>{{ format_vnd($shipping) }}</dd></div></dl>
                    <p class="mt-4 border-t border-line pt-4 text-2xl font-bold">{{ format_vnd($subtotal + $shipping) }}</p>
                    <x-button type="submit" variant="accent" class="mt-5 w-full">{{ __('ui.checkout.place_order') }}</x-button>
                </x-card>
            </form>
        @endif
    </div>
</x-app-layout>
