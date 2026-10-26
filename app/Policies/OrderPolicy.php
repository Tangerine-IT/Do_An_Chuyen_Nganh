<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->is($order->user) || $user->hasRole(UserRole::Staff, UserRole::Admin);
    }
}
