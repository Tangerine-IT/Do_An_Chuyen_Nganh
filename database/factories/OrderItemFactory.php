<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Name, SKU and unit price are snapshotted from the product, like real checkout does.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_name' => fn (array $attributes) => Product::findOrFail($attributes['product_id'])->name,
            'product_sku' => fn (array $attributes) => Product::findOrFail($attributes['product_id'])->sku,
            'unit_price' => fn (array $attributes) => Product::findOrFail($attributes['product_id'])->current_price,
            'quantity' => fake()->numberBetween(1, 3),
        ];
    }
}
