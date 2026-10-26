<?php

namespace App\Enums;

enum LoyaltyReason: string
{
    case Earned = 'earned';
    case Redeemed = 'redeemed';
    case Adjustment = 'adjustment';
    case Reversed = 'reversed';

    public function label(): string
    {
        return __('enums.loyalty_reason.'.$this->value);
    }
}
