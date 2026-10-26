<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CartPage extends Component
{
    public function updateQuantity(int $productId, int $quantity, CartService $cartService): void
    {
        $cartService->update($this->cart(), $productId, $quantity);
        $this->dispatch('toast', type: 'success', message: __('ui.cart.updated'));
    }

    public function remove(int $productId, CartService $cartService): void
    {
        $cartService->remove($this->cart(), $productId);
        $this->dispatch('toast', type: 'success', message: __('ui.cart.removed'));
    }

    public function clear(CartService $cartService): void
    {
        $cartService->clear($this->cart());
        $this->dispatch('toast', type: 'success', message: __('ui.cart.cleared'));
    }

    public function render(CartService $cartService): View
    {
        $cart = $this->cart($cartService);
        $cart->load('items.product.primaryImage');

        return view('livewire.cart-page', compact('cart'));
    }

    private function cart(?CartService $cartService = null): Cart
    {
        $cartService ??= app(CartService::class);

        return $cartService->current(auth()->user(), session()->getId());
    }
}
