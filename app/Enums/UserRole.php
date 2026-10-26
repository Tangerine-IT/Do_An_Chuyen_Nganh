<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Staff = 'staff';
    case Admin = 'admin';

    public function label(): string
    {
        return __('enums.user_role.'.$this->value);
    }
}
