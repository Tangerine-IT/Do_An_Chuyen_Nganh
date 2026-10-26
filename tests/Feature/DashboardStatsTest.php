<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\DashboardStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_summary_counts_revenue_orders_and_customers(): void
    {
        $this->travelTo(now()->setDay(15)->setTime(12, 0));
        $customers = User::factory()->count(2)->create(['role' => 'customer']);
        Order::factory()->create(['user_id' => $customers[0]->id, 'total' => 300000, 'placed_at' => now()->subHours(2)]);
        Order::factory()->create(['user_id' => $customers[0]->id, 'total' => 100000, 'status' => OrderStatus::Cancelled, 'placed_at' => now()]);

        $summary = app(DashboardStatsService::class)->summary();

        $this->assertSame(300000, $summary['todayRevenue']);
        $this->assertSame(2, $summary['newOrders']);
        $this->assertSame(2, $summary['newCustomers']);
    }

    public function test_revenue_series_groups_non_cancelled_orders_by_day(): void
    {
        $this->travelTo(now()->setTime(12, 0));
        Order::factory()->create(['total' => 125000, 'placed_at' => now()->subDay()]);
        Order::factory()->create(['total' => 50000, 'status' => OrderStatus::Cancelled, 'placed_at' => now()->subDay()]);

        $series = app(DashboardStatsService::class)->revenueSeries('7d');

        $this->assertCount(7, $series['labels']);
        $this->assertContains(125000, $series['values']);
        $this->assertNotContains(175000, $series['values']);
    }

    public function test_top_products_and_low_stock_are_ranked(): void
    {
        $product = Product::factory()->create(['stock' => 2]);
        $order = Order::factory()->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 100000,
            'quantity' => 4,
        ]);

        $stats = app(DashboardStatsService::class);

        $this->assertSame($product->name, $stats->topProducts()[0]['name']);
        $this->assertTrue($stats->lowStockProducts()->contains($product));
    }
}
