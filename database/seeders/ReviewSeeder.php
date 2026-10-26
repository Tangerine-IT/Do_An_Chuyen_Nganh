<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * A handful of product reviews from each customer. Requires UserSeeder and ProductSeeder.
 */
class ReviewSeeder extends Seeder
{
    private const REVIEWS_PER_CUSTOMER = 4;

    public function run(): void
    {
        $products = Product::active()->inRandomOrder()->get();
        $customers = User::where('role', UserRole::Customer)->get();

        foreach ($customers as $position => $customer) {
            // Slice the shuffled list so a customer never reviews the same product twice.
            $chosen = $products->slice($position * self::REVIEWS_PER_CUSTOMER, self::REVIEWS_PER_CUSTOMER);

            foreach ($chosen as $product) {
                [$rating, $title, $body] = $this->randomReview();

                Review::factory()->for($customer)->for($product)->create([
                    'rating' => $rating,
                    'title' => $title,
                    'body' => $body,
                ]);
            }
        }
    }

    /**
     * @return array{int, string, string}
     */
    private function randomReview(): array
    {
        $reviews = [
            [5, 'Bé nhà mình rất thích', 'Bé ăn rất ngon miệng, lông mượt hơn sau vài tuần dùng. Sẽ ủng hộ shop dài dài.'],
            [5, 'Chất lượng tốt', 'Sản phẩm đúng như mô tả, đóng gói cẩn thận, giao hàng nhanh trong ngày.'],
            [5, 'Đáng tiền', 'Giá hợp lý so với chất lượng. Mình đã mua lại lần thứ ba.'],
            [4, 'Khá ổn', 'Dùng tốt, bé nhà mình ăn hết sạch. Mong shop thêm nhiều size hơn.'],
            [4, 'Hài lòng', 'Giao hàng nhanh, sản phẩm đúng hàng. Trừ một sao vì bao bì hơi móp.'],
            [3, 'Tạm được', 'Bé ăn được nhưng không quá mê. Có lẽ sẽ thử vị khác.'],
        ];

        return $reviews[array_rand($reviews)];
    }
}
