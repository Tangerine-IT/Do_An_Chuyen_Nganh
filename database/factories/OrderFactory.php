<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(100, 2000) * 1000;
        $shippingFee = 30000;

        return [
            'user_id' => User::factory(),
            'coupon_id' => null,
            'order_number' => 'PS'.now()->format('ymd').fake()->unique()->numerify('######'),
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
            'payment_method' => PaymentMethod::Cod,
            'subtotal' => $subtotal,
            'discount_total' => 0,
            'shipping_fee' => $shippingFee,
            'total' => $subtotal + $shippingFee,
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->numerify('09########'),
            'shipping_address' => fake()->numberBetween(1, 300).' '.fake()->randomElement(['Nguyễn Trãi', 'Lê Lợi', 'Trần Hưng Đạo', 'Hai Bà Trưng']),
            'shipping_ward' => 'Phường '.fake()->numberBetween(1, 15),
            'shipping_district' => 'Quận '.fake()->numberBetween(1, 12),
            'shipping_province' => fake()->randomElement(['TP. Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng']),
            'note' => null,
            'placed_at' => now(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Completed,
            'payment_status' => PaymentStatus::Paid,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => OrderStatus::Cancelled]);
    }
}
