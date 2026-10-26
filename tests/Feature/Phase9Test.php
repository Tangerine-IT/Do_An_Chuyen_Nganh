<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class Phase9Test extends TestCase
{
    use RefreshDatabase;

    public function test_coupon_rejects_expired_and_exhausted_codes(): void
    {
        $user = User::factory()->create();

        $this->expectException(RuntimeException::class);
        app(CouponService::class)->discountFor(Coupon::factory()->expired()->create()->code, 500000, $user);
    }

    public function test_coupon_calculates_discount_and_rejects_reuse(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->fixed(30000)->create(['min_order_total' => 0]);

        $this->assertSame(30000, app(CouponService::class)->discountFor($coupon->code, 100000, $user));
        $coupon->usages()->create(['user_id' => $user->id, 'order_id' => Order::factory()->create(['user_id' => $user->id])->id]);

        $this->expectException(RuntimeException::class);
        app(CouponService::class)->discountFor($coupon->code, 100000, $user);
    }

    public function test_wishlist_can_toggle_and_review_requires_completed_purchase(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->postJson(route('wishlist.toggle', $product))->assertJson(['wishlisted' => true]);
        $this->assertDatabaseHas('wishlists', ['user_id' => $user->id, 'product_id' => $product->id]);
        $this->actingAs($user)->postJson(route('wishlist.toggle', $product))->assertJson(['wishlisted' => false]);

        $this->actingAs($user)->post(route('reviews.store', $product), ['rating' => 5, 'body' => 'Tốt'])->assertForbidden();

        $order = Order::factory()->completed()->create(['user_id' => $user->id]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);
        $this->actingAs($user)->post(route('reviews.store', $product), ['rating' => 5, 'body' => 'Tốt'])->assertRedirect();
        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'product_id' => $product->id]);
    }
}
