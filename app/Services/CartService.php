<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function current(?User $user, string $sessionId): Cart
    {
        if ($user) {
            return Cart::firstOrCreate(['user_id' => $user->id], ['session_id' => null]);
        }

        return Cart::firstOrCreate(['session_id' => $sessionId], ['user_id' => null]);
    }

    public function mergeGuestCart(User $user, string $guestSessionId): void
    {
        $guest = Cart::query()->where('session_id', $guestSessionId)->with('items')->first();
        if (! $guest) {
            return;
        }

        DB::transaction(function () use ($guest, $user): void {
            $customerCart = $this->current($user, '');

            foreach ($guest->items as $guestItem) {
                $item = $customerCart->items()->firstOrNew(['product_id' => $guestItem->product_id]);
                $product = Product::find($guestItem->product_id);
                if (! $product || ! $product->is_active || $product->stock < 1) {
                    continue;
                }

                $item->quantity = min($product->stock, (int) $item->quantity + $guestItem->quantity);
                $item->save();
            }

            $guest->delete();
        });
    }

    public function add(Cart $cart, int $productId, int $quantity): void
    {
        $product = Product::query()->active()->find($productId);
        if (! $product) {
            throw (new ModelNotFoundException)->setModel(Product::class, [$productId]);
        }

        if ($product->stock < 1) {
            throw new \DomainException(__('ui.cart.out_of_stock'));
        }

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = min($product->stock, max(1, (int) $item->quantity + $quantity));
        $item->save();
    }

    public function update(Cart $cart, int $productId, int $quantity): void
    {
        $item = $cart->items()->where('product_id', $productId)->firstOrFail();
        $product = Product::query()->active()->find($productId);
        if (! $product || $product->stock < 1) {
            $item->delete();

            return;
        }

        $item->update(['quantity' => min($product->stock, max(1, $quantity))]);
    }

    public function remove(Cart $cart, int $productId): void
    {
        $cart->items()->where('product_id', $productId)->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
