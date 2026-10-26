<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_to_cart_and_cart_is_merged_after_login(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $response = $this->post(route('cart.items.store'), ['product_id' => $product->id, 'quantity' => 2]);
        $response->assertRedirect();

        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_customer_can_place_order_and_stock_and_cart_are_updated(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->for($user)->create(['is_default' => true]);
        $product = Product::factory()->create(['price' => 100000, 'stock' => 5]);

        $this->actingAs($user)->post(route('cart.items.store'), ['product_id' => $product->id, 'quantity' => 2]);
        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
        ]);

        $order = Order::query()->first();
        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame(230000, $order->total);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'unit_price' => 100000,
            'quantity' => 2,
        ]);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_order_keeps_purchase_price_after_product_changes(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->for($user)->create();
        $product = Product::factory()->create(['price' => 120000, 'stock' => 3]);

        $this->actingAs($user)->post(route('cart.items.store'), ['product_id' => $product->id]);
        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
        ]);
        $product->update(['price' => 250000]);

        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'unit_price' => 120000]);
    }

    public function test_order_cancellation_restores_stock(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->for($user)->create();
        $product = Product::factory()->create(['stock' => 5]);

        $this->actingAs($user)->post(route('cart.items.store'), ['product_id' => $product->id, 'quantity' => 2]);
        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
        ]);
        $order = Order::query()->firstOrFail();

        $response = $this->actingAs($user)->post(route('orders.cancel', $order));

        $response->assertRedirect();
        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_order_cannot_be_placed_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->for($user)->create();
        $product = Product::factory()->create(['stock' => 1]);

        $this->actingAs($user)->post(route('cart.items.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $product->update(['stock' => 0]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHasErrors('checkout');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->for($owner)->create();

        $this->actingAs($other)->get(route('orders.show', $order))->assertForbidden();
    }
}
