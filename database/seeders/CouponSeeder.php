<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

/**
 * Valid coupons for the demo, plus an expired and an exhausted one for testing validation.
 */
class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::factory()->create([
            'code' => 'WELCOME10',
            'description' => 'Giảm 10% cho đơn hàng đầu tiên (tối đa 50.000 ₫)',
            'type' => CouponType::Percent,
            'value' => 10,
            'min_order_total' => 200000,
            'max_discount' => 50000,
            'usage_limit' => 500,
            'expires_at' => now()->addDays(90),
        ]);

        Coupon::factory()->fixed(30000)->create([
            'code' => 'FREESHIP30',
            'description' => 'Giảm 30.000 ₫ phí vận chuyển cho đơn từ 300.000 ₫',
            'min_order_total' => 300000,
            'usage_limit' => 200,
            'expires_at' => now()->addDays(60),
        ]);

        Coupon::factory()->fixed(100000)->create([
            'code' => 'PETLOVER100',
            'description' => 'Giảm 100.000 ₫ cho đơn từ 1.000.000 ₫',
            'min_order_total' => 1000000,
            'usage_limit' => 50,
            'expires_at' => now()->addDays(30),
        ]);

        Coupon::factory()->expired()->create([
            'code' => 'TET2026',
            'description' => 'Ưu đãi Tết (đã hết hạn)',
            'value' => 15,
            'max_discount' => 100000,
        ]);

        Coupon::factory()->exhausted(100)->create([
            'code' => 'FLASH50',
            'description' => 'Ưu đãi giờ vàng (đã hết lượt dùng)',
            'value' => 20,
            'max_discount' => 80000,
        ]);
    }
}
