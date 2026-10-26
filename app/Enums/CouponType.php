<?php

namespace App\Enums;

enum CouponType: string
{
    /** coupons.value is a percentage (1-100). */
    case Percent = 'percent';

    /** coupons.value is a whole VND amount. */
    case Fixed = 'fixed';

    public function label(): string
    {
        return __('enums.coupon_type.'.$this->value);
    }
}
