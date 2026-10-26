<?php

namespace Database\Factories;

use App\Enums\LoyaltyReason;
use App\Models\LoyaltyPoint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoyaltyPoint>
 */
class LoyaltyPointFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $points = fake()->numberBetween(10, 500);

        return [
            'user_id' => User::factory(),
            'order_id' => null,
            'points' => $points,
            'balance_after' => $points,
            'reason' => LoyaltyReason::Earned,
            'description' => 'Tích điểm từ đơn hàng',
        ];
    }
}
