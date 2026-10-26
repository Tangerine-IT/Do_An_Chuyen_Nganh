<?php

namespace App\Contracts;

use App\Models\Coupon;
use App\Models\User;

interface CouponService
{
    public function discountFor(?string $code, int $subtotal, User $user): int;

    public function couponFor(?string $code, int $subtotal, User $user): ?Coupon;
}
