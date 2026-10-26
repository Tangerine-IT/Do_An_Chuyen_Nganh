<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->is($booking->user) || $user->hasRole(UserRole::Staff, UserRole::Admin);
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }
}
