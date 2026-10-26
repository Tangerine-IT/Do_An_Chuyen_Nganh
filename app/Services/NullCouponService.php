<?php

namespace App\Services;

use App\Contracts\CouponService;
use App\Models\Coupon;
use App\Models\User;

class NullCouponService implements CouponService
{
    public function discountFor(?string $code, int $subtotal, User $user): int
    {
        return 0;
    }

    public function couponFor(?string $code, int $subtotal, User $user): ?Coupon
    {
        return null;
    }
}
