<?php

namespace Tests\Feature\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_has_user_coupon_items_and_payments(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create();
        $order = Order::factory()->for($user)->create(['coupon_id' => $coupon->id]);
        OrderItem::factory()->count(2)->for($order)->create();
        Payment::factory()->for($order)->create();

        $this->assertTrue($order->user->is($user));
        $this->assertTrue($order->coupon->is($coupon));
        $this->assertCount(2, $order->items);
        $this->assertCount(1, $order->payments);
        $this->assertTrue($coupon->orders->first()->is($order));
    }

    public function test_order_attributes_are_cast(): void
    {
        $order = Order::factory()->create()->fresh();

        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame(PaymentStatus::Pending, $order->payment_status);
        $this->assertSame(PaymentMethod::Cod, $order->payment_method);
        $this->assertIsInt($order->subtotal);
        $this->assertIsInt($order->total);
        $this->assertInstanceOf(Carbon::class, $order->placed_at);
    }

    public function test_factory_totals_are_consistent(): void
    {
        $order = Order::factory()->create();

        $this->assertSame($order->subtotal - $order->discount_total + $order->shipping_fee, $order->total);
    }

    public function test_order_number_must_be_unique(): void
    {
        Order::factory()->create(['order_number' => 'PS260101000001']);

        $this->expectException(QueryException::class);

        Order::factory()->create(['order_number' => 'PS260101000001']);
    }

    public function test_order_survives_deleting_its_user_and_coupon(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create();
        $order = Order::factory()->for($user)->create(['coupon_id' => $coupon->id]);

        $coupon->delete();
        $user->delete();

        $order = $order->fresh();
        $this->assertNull($order->user_id);
        $this->assertNull($order->coupon_id);
    }

    public function test_deleting_an_order_removes_items_and_payments(): void
    {
        $order = Order::factory()->create();
        OrderItem::factory()->for($order)->create();
        Payment::factory()->for($order)->create();

        $order->delete();

        $this->assertSame(0, OrderItem::count());
        $this->assertSame(0, Payment::count());
    }

    public function test_order_item_snapshots_the_product_and_computes_the_line_total(): void
    {
        $product = Product::factory()->create(['price' => 100000, 'sale_price' => null]);
        $item = OrderItem::factory()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 100000,
            'quantity' => 3,
        ]);

        $this->assertSame(300000, $item->line_total);
        $this->assertTrue($item->product->is($product));
    }

    public function test_order_item_price_does_not_follow_later_product_price_changes(): void
    {
        $product = Product::factory()->create(['price' => 100000, 'sale_price' => null]);
        $item = OrderItem::factory()->create(['product_id' => $product->id, 'quantity' => 1]);

        $product->update(['price' => 150000]);

        $this->assertSame(100000, $item->fresh()->unit_price);
    }

    public function test_order_item_keeps_its_snapshot_when_the_product_is_deleted(): void
    {
        $product = Product::factory()->create(['name' => 'Hạt Chó Test']);
        $item = OrderItem::factory()->create(['product_id' => $product->id, 'quantity' => 2]);
        $unitPrice = $item->unit_price;

        $product->delete();

        $item = $item->fresh();
        $this->assertNull($item->product_id);
        $this->assertSame('Hạt Chó Test', $item->product_name);
        $this->assertSame($unitPrice, $item->unit_price);
    }

    public function test_payment_attributes_are_cast(): void
    {
        $payment = Payment::factory()->paidVia(PaymentMethod::Vnpay)->create([
            'gateway_response' => ['vnp_ResponseCode' => '00'],
        ])->fresh();

        $this->assertSame(PaymentMethod::Vnpay, $payment->method);
        $this->assertSame(PaymentStatus::Paid, $payment->status);
        $this->assertSame(['vnp_ResponseCode' => '00'], $payment->gateway_response);
        $this->assertInstanceOf(Carbon::class, $payment->paid_at);
        $this->assertTrue($payment->order->is(Order::first()));
    }

    public function test_a_gateway_transaction_can_only_be_recorded_once(): void
    {
        Payment::factory()->create(['method' => PaymentMethod::Momo, 'transaction_id' => 'TXN-1']);

        $this->expectException(QueryException::class);

        Payment::factory()->create(['method' => PaymentMethod::Momo, 'transaction_id' => 'TXN-1']);
    }

    public function test_payments_without_a_transaction_id_do_not_conflict(): void
    {
        Payment::factory()->count(2)->create(['method' => PaymentMethod::Cod, 'transaction_id' => null]);

        $this->assertSame(2, Payment::count());
    }

    public function test_cart_has_items_for_a_user_or_a_guest_session(): void
    {
        $userCart = Cart::factory()->create();
        $guestCart = Cart::factory()->guest()->create();
        $item = CartItem::factory()->for($userCart)->create(['quantity' => 2]);

        $this->assertTrue($userCart->user->is(User::first()));
        $this->assertNull($guestCart->user_id);
        $this->assertNotEmpty($guestCart->session_id);
        $this->assertTrue($userCart->items->first()->is($item));
        $this->assertTrue($item->cart->is($userCart));
        $this->assertSame(2, $item->quantity);
    }

    public function test_a_product_appears_once_per_cart(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create();
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id]);

        $this->expectException(QueryException::class);

        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id]);
    }

    public function test_a_user_has_only_one_cart(): void
    {
        $user = User::factory()->create();
        Cart::factory()->for($user)->create();

        $this->expectException(QueryException::class);

        Cart::factory()->for($user)->create();
    }

    public function test_coupon_attributes_are_cast_and_usage_is_not_mass_assignable(): void
    {
        $coupon = Coupon::create([
            'code' => 'TEST10',
            'type' => 'percent',
            'value' => 10,
            'used_count' => 99,
            'expires_at' => now()->addDay(),
        ])->fresh();

        $this->assertSame(0, $coupon->used_count);
        $this->assertSame(10, $coupon->value);
        $this->assertTrue($coupon->is_active);
        $this->assertInstanceOf(Carbon::class, $coupon->expires_at);
    }

    public function test_coupon_factory_states_describe_unusable_coupons(): void
    {
        $expired = Coupon::factory()->expired()->create();
        $exhausted = Coupon::factory()->exhausted(5)->create();

        $this->assertTrue($expired->expires_at->isPast());
        $this->assertSame($exhausted->usage_limit, $exhausted->used_count);
    }

    public function test_coupon_code_must_be_unique(): void
    {
        Coupon::factory()->create(['code' => 'DUPLICATE']);

        $this->expectException(QueryException::class);

        Coupon::factory()->create(['code' => 'DUPLICATE']);
    }
}
