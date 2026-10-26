<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Pet;
use App\Models\Product;
use App\Models\User;
use App\Services\FeedingEstimator;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyAndFeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_loyalty_earns_idempotently_and_redeems_points(): void
    {
        $user = User::factory()->create(['loyalty_points' => 0]);
        $order = Order::factory()->for($user)->create(['total' => 200000, 'status' => OrderStatus::Completed]);
        $service = app(LoyaltyService::class);

        $service->earnOrder($order->fresh('user'));
        $service->earnOrder($order->fresh('user'));
        $this->assertSame(20, $user->fresh()->loyalty_points);
        $service->redeem($user->fresh(), 10, $order);
        $this->assertSame(10, $user->fresh()->loyalty_points);
        $this->assertDatabaseHas('loyalty_points', ['order_id' => $order->id, 'points' => -10]);
    }

    public function test_feeding_estimate_calculates_runout_and_reorder_is_owned(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->for($user)->create(['weight_kg' => 5]);
        $product = Product::factory()->create(['package_size_grams' => 1000, 'daily_grams_per_kg' => 20]);
        $estimate = app(FeedingEstimator::class)->estimate($pet, $product);

        $this->assertSame(100.0, (float) $estimate->daily_grams);
        $this->assertSame(now()->addDays(10)->toDateString(), $estimate->runs_out_at->toDateString());
        $this->actingAs($user)->post(route('feeding.reorder', $estimate))->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 1]);
    }

    public function test_feeding_estimate_cannot_be_reordered_by_another_user(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->for($user)->create(['weight_kg' => 5]);
        $product = Product::factory()->create(['package_size_grams' => 1000, 'daily_grams_per_kg' => 20]);
        $estimate = app(FeedingEstimator::class)->estimate($pet, $product);

        $this->actingAs(User::factory()->create())->post(route('feeding.reorder', $estimate))->assertForbidden();
    }
}
