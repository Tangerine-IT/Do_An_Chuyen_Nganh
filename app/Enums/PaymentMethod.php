<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cod = 'cod';
    case Vnpay = 'vnpay';
    case Momo = 'momo';

    public function label(): string
    {
        return __('enums.payment_method.'.$this->value);
    }
}
