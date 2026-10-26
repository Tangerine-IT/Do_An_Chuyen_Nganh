<?php

namespace App\View\Composers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Pet;
use Illuminate\View\View;

/**
 * Supplies the public layout (header and footer) with the category menu and the cart item count.
 */
class SiteNavigationComposer
{
    public function compose(View $view): void
    {
        $currentPet = auth()->user()?->isCustomer()
            ? Pet::query()->where('user_id', auth()->id())->find(session('current_pet_id'))
            : null;

        $view->with([
            'navCategories' => Category::query()
                ->active()
                ->roots()
                ->orderBy('sort_order')
                ->with(['children' => fn ($children) => $children->where('is_active', true)])
                ->get(),
            'cartCount' => $this->cartCount(),
            'currentPet' => $currentPet,
            'accountPets' => auth()->user()?->isCustomer() ? auth()->user()->pets()->orderBy('name')->get() : collect(),
            'unreadNotifications' => auth()->check() ? auth()->user()->unreadNotifications()->latest()->limit(5)->get() : collect(),
            'notificationCount' => auth()->check() ? auth()->user()->unreadNotifications()->count() : 0,
        ]);
    }

    /**
     * Total quantity in the current cart: the user's cart when logged in, otherwise the guest session cart.
     */
    private function cartCount(): int
    {
        $cart = Cart::query()
            ->when(
                auth()->check(),
                fn ($query) => $query->where('user_id', auth()->id()),
                fn ($query) => $query->where('session_id', session()->getId()),
            )
            ->withSum('items', 'quantity')
            ->first();

        return (int) ($cart?->items_sum_quantity ?? 0);
    }
}
