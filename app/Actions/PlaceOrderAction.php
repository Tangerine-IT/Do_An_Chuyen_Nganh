<?php

namespace App\Actions;

use App\Contracts\CouponService;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PlaceOrderAction
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
        private readonly LoyaltyService $loyaltyService,
    ) {}

    /**
     * @param  array{address_id: int, note?: string|null, payment_method?: string, coupon_code?: string|null}  $data
     */
    public function execute(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data): Order {
            $address = $user->addresses()->findOrFail($data['address_id']);
            $cart = $user->cart()->with('items.product')->first();
            if (! $cart || $cart->items->isEmpty()) {
                throw new RuntimeException(__('ui.cart.empty'));
            }

            $items = [];
            $subtotal = 0;
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product()->lockForUpdate()->first();
                if (! $product || ! $product->is_active || $product->stock < $cartItem->quantity) {
                    throw new RuntimeException(__('ui.checkout.stock_changed'));
                }

                $unitPrice = $product->current_price;
                $quantity = (int) $cartItem->quantity;
                $subtotal += $unitPrice * $quantity;
                $items[] = compact('product', 'unitPrice', 'quantity');
            }

            $coupon = $this->couponService->couponFor($data['coupon_code'] ?? null, $subtotal, $user);
            if ($coupon?->category_id && ! collect($items)->contains(fn (array $item): bool => $item['product']->category_id === $coupon->category_id)) {
                throw new RuntimeException(__('ui.coupon.category_invalid'));
            }
            $discount = min($subtotal, $this->couponService->discountFor($data['coupon_code'] ?? null, $subtotal, $user));
            $loyaltyPoints = min((int) ($data['loyalty_points'] ?? 0), $user->loyalty_points);
            $loyaltyDiscount = min(
                $loyaltyPoints * (int) config('loyalty.redeem_value'),
                (int) floor($subtotal * ((int) config('loyalty.max_redeem_percent') / 100))
            );
            $shippingFee = $subtotal >= config('petshop.shipping.free_threshold')
                ? 0
                : (int) config('petshop.shipping.fee');
            $paymentMethod = PaymentMethod::tryFrom($data['payment_method'] ?? PaymentMethod::Cod->value);

            $order = $user->orders()->create([
                'coupon_id' => $coupon?->id,
                'order_number' => 'PS'.now()->format('ymd').Str::upper(Str::random(8)),
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Pending,
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'discount_total' => $discount + $loyaltyDiscount,
                'shipping_fee' => $shippingFee,
                'total' => $subtotal - $discount + $shippingFee,
                'recipient_name' => $address->recipient_name,
                'recipient_phone' => $address->phone,
                'shipping_address' => $address->address_line,
                'shipping_ward' => $address->ward,
                'shipping_district' => $address->district,
                'shipping_province' => $address->province,
                'note' => $data['note'] ?? null,
                'placed_at' => now(),
                'payment_expires_at' => $paymentMethod === PaymentMethod::Cod ? null : now()->addMinutes(15),
            ]);

            foreach ($items as $item) {
                $item['product']->decrement('stock', $item['quantity']);
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_sku' => $item['product']->sku,
                    'unit_price' => $item['unitPrice'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $order->payments()->create([
                'method' => $paymentMethod,
                'status' => PaymentStatus::Pending,
                'amount' => $order->total,
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
                $coupon->usages()->create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                ]);
            }
            if ($loyaltyDiscount > 0) {
                $this->loyaltyService->redeem($user, (int) floor($loyaltyDiscount / config('loyalty.redeem_value')), $order);
            }

            $this->cartService->clear($cart);

            return $order;
        });
    }

    public function cancel(User $user, Order $order): void
    {
        DB::transaction(function () use ($user, $order): void {
            $order = Order::query()->whereKey($order->id)->with('items')->lockForUpdate()->firstOrFail();
            if (! $order->user()->whereKey($user->id)->exists()) {
                abort(403);
            }
            if ($order->status !== OrderStatus::Pending) {
                throw new RuntimeException(__('ui.orders.cancel_not_allowed'));
            }

            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $item->product()->lockForUpdate()->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => OrderStatus::Cancelled]);
        });
    }
}
