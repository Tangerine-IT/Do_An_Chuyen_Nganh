<?php

namespace App\Enums;

/**
 * Used both for orders.payment_status and payments.status.
 */
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return __('enums.payment_status.'.$this->value);
    }
}
