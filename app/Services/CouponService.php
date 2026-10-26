<?php

namespace App\Services;

use App\Contracts\CouponService as CouponServiceContract;
use App\Models\Coupon;
use App\Models\User;
use RuntimeException;

class CouponService implements CouponServiceContract
{
    public function discountFor(?string $code, int $subtotal, User $user): int
    {
        if (blank($code)) {
            return 0;
        }

        $coupon = Coupon::query()->whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])->lockForUpdate()->first();
        $this->validate($coupon, $subtotal, $user);

        return $this->calculate($coupon, $subtotal);
    }

    public function couponFor(?string $code, int $subtotal, User $user): ?Coupon
    {
        if (blank($code)) {
            return null;
        }

        $coupon = Coupon::query()->whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])->first();
        $this->validate($coupon, $subtotal, $user);

        return $coupon;
    }

    public function validate(?Coupon $coupon, int $subtotal, User $user): void
    {
        if (! $coupon || ! $coupon->is_active) {
            throw new RuntimeException(__('ui.coupon.invalid'));
        }
        if ($coupon->user_id !== null && $coupon->user_id !== $user->id) {
            throw new RuntimeException(__('ui.coupon.invalid'));
        }
        if (($coupon->starts_at && now()->lt($coupon->starts_at)) || ($coupon->expires_at && now()->gt($coupon->expires_at))) {
            throw new RuntimeException(__('ui.coupon.expired'));
        }
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw new RuntimeException(__('ui.coupon.used_up'));
        }
        if ($coupon->usages()->where('user_id', $user->id)->exists()) {
            throw new RuntimeException(__('ui.coupon.already_used'));
        }
        if ($subtotal < $coupon->min_order_total) {
            throw new RuntimeException(__('ui.coupon.minimum_error', ['amount' => format_vnd($coupon->min_order_total)]));
        }
    }

    private function calculate(Coupon $coupon, int $subtotal): int
    {
        $discount = $coupon->type->value === 'percent'
            ? (int) floor($subtotal * $coupon->value / 100)
            : $coupon->value;

        return min($subtotal, $coupon->max_discount ? min($discount, $coupon->max_discount) : $discount);
    }
}
