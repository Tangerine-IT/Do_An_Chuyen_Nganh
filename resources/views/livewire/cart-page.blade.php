<div>
    @if ($cart->items->isEmpty())
        <x-empty-state :title="__('ui.cart.empty')" class="mt-8">
            <x-slot:action><x-button :href="route('products.index')">{{ __('ui.cart.continue_shopping') }}</x-button></x-slot:action>
        </x-empty-state>
    @else
        @php($subtotal = $cart->items->sum(fn ($item) => $item->product->current_price * $item->quantity))
        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_22rem]">
            <div class="space-y-4">
                @foreach ($cart->items as $item)
                    <x-card class="flex gap-4">
                        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-cream-100">@if ($item->product->primaryImage)<img src="{{ storage_url($item->product->primaryImage->path) }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">@endif</div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-semibold text-primary-900 hover:underline">{{ $item->product->name }}</a>
                            <p class="mt-1 text-sm text-ink-muted">{{ format_vnd($item->product->current_price) }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <input type="number" min="1" max="{{ $item->product->stock }}" value="{{ $item->quantity }}" wire:change="updateQuantity({{ $item->product->id }}, $event.target.value)" class="form-control w-20 py-2">
                                <button type="button" wire:click="remove({{ $item->product->id }})" class="text-sm font-semibold text-red-700 hover:underline">{{ __('ui.cart.remove') }}</button>
                            </div>
                            @if ($item->quantity >= $item->product->stock)
                                <p class="mt-2 text-xs text-amber-700">{{ __('ui.cart.stock_limit', ['count' => $item->product->stock]) }}</p>
                            @endif
                        </div>
                        <p class="font-bold">{{ format_vnd($item->product->current_price * $item->quantity) }}</p>
                    </x-card>
                @endforeach
            </div>
            <x-card class="h-fit">
                <h2 class="text-xl">{{ __('ui.cart.subtotal') }}</h2>
                <p class="mt-3 text-2xl font-bold text-primary-800">{{ format_vnd($subtotal) }}</p>
                <x-button :href="route('checkout.create')" variant="accent" class="mt-6 w-full">{{ __('ui.cart.checkout') }}</x-button>
                <button type="button" wire:click="clear" class="mt-3 w-full text-sm font-semibold text-ink-muted hover:text-primary-800">{{ __('ui.cart.cleared') }}</button>
            </x-card>
        </div>
    @endif
</div>
