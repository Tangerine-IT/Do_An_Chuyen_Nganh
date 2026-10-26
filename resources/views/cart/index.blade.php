<x-app-layout :title="__('ui.cart.title')">
    <div class="container-page py-10 sm:py-14">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">{{ __('ui.cart.label') }}</p>
                <h1 class="mt-2 text-3xl">{{ __('ui.cart.title') }}</h1>
            </div>
            @if ($cart->items->isNotEmpty())
                <form method="POST" action="{{ route('cart.clear') }}">@csrf @method('DELETE')<x-button type="submit" variant="ghost" size="sm">{{ __('ui.cart.cleared') }}</x-button></form>
            @endif
        </div>

        <livewire:cart-page />
    </div>
</x-app-layout>
