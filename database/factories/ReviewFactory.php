<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'rating' => fake()->randomElement([3, 4, 4, 5, 5, 5]),
            'title' => fake()->randomElement(['Rất hài lòng', 'Đáng tiền', 'Bé nhà mình rất thích']),
            'body' => fake()->randomElement([
                'Sản phẩm đúng như mô tả, đóng gói cẩn thận, giao hàng nhanh.',
                'Bé ăn rất ngon miệng, lông mượt hơn sau vài tuần dùng.',
                'Chất lượng tốt so với giá tiền, mình sẽ mua lại.',
            ]),
            'is_approved' => true,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['is_approved' => false]);
    }
}
