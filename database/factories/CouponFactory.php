<?php

namespace Database\Factories;

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'PS'.strtoupper(Str::random(8)),
            'description' => 'Giảm giá cho đơn hàng của bạn',
            'type' => CouponType::Percent,
            'value' => 10,
            'min_order_total' => 200000,
            'max_discount' => 50000,
            'usage_limit' => null,
            'used_count' => 0,
            'starts_at' => null,
            'expires_at' => now()->addDays(30),
            'is_active' => true,
        ];
    }

    public function fixed(int $amount = 30000): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CouponType::Fixed,
            'value' => $amount,
            'max_discount' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subDays(60),
            'expires_at' => now()->subDays(1),
        ]);
    }

    public function exhausted(int $limit = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_limit' => $limit,
            'used_count' => $limit,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
