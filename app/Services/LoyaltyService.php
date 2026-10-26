<?php

namespace App\Services;

use App\Enums\LoyaltyReason;
use App\Models\Booking;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    public function earnOrder(Order $order): ?LoyaltyPoint
    {
        return $this->earn($order->user, (int) floor($order->total / 10000) * (int) config('loyalty.order_points_per_10000'), 'order', $order->id, $order->id);
    }

    public function earnBooking(Booking $booking): ?LoyaltyPoint
    {
        return $this->earn($booking->user, (int) floor($booking->price / 10000) * (int) config('loyalty.booking_points_per_10000'), 'booking', $booking->id, null, $booking->id);
    }

    public function redeem(User $user, int $points, ?Order $order = null): LoyaltyPoint
    {
        return DB::transaction(function () use ($user, $points, $order): LoyaltyPoint {
            $locked = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($points < 1 || $points > $locked->loyalty_points) {
                throw new \RuntimeException(__('ui.loyalty.insufficient'));
            }
            $locked->loyalty_points -= $points;
            $locked->save();

            return $locked->loyaltyTransactions()->create([
                'order_id' => $order?->id,
                'points' => -$points,
                'balance_after' => $locked->loyalty_points,
                'reason' => LoyaltyReason::Redeemed,
                'description' => __('ui.loyalty.redeemed_description'),
            ]);
        });
    }

    public function tier(User $user): string
    {
        return collect(config('loyalty.tiers'))->filter(fn (array $tier): bool => $user->loyalty_points >= $tier['min'])->keys()->last() ?: 'bronze';
    }

    /** @return list<string> */
    public function badges(User $user): array
    {
        return array_values(array_filter([
            $user->loyalty_points >= 100 ? 'Khách hàng thân thiết' : null,
            $user->orders()->where('status', 'completed')->count() >= 5 ? 'Người bạn đồng hành' : null,
            $user->adoptionApplications()->where('status', 'approved')->exists() ? 'Người yêu động vật' : null,
        ]));
    }

    private function earn(User $user, int $points, string $source, int $sourceId, ?int $orderId = null, ?int $bookingId = null): ?LoyaltyPoint
    {
        if ($points < 1 || $user->loyaltyTransactions()->where('description', $source.':'.$sourceId)->exists()) {
            return null;
        }

        return DB::transaction(function () use ($user, $points, $source, $sourceId, $orderId, $bookingId): LoyaltyPoint {
            $locked = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $locked->loyalty_points += $points;
            $locked->save();

            return $locked->loyaltyTransactions()->create([
                'order_id' => $orderId,
                'booking_id' => $bookingId,
                'points' => $points,
                'balance_after' => $locked->loyalty_points,
                'reason' => LoyaltyReason::Earned,
                'description' => $source.':'.$sourceId,
            ]);
        });
    }
}
