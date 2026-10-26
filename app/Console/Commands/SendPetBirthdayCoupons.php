<?php

namespace App\Console\Commands;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\Pet;
use App\Notifications\PetBirthdayNotification;
use Illuminate\Console\Command;

class SendPetBirthdayCoupons extends Command
{
    protected $signature = 'pets:send-birthday-coupons';

    protected $description = 'Create birthday coupons and notify pet owners';

    public function handle(): int
    {
        $created = 0;
        Pet::query()
            ->whereNotNull('birthday')
            ->whereMonth('birthday', now()->month)
            ->whereDay('birthday', now()->day)
            ->with('user')
            ->each(function (Pet $pet) use (&$created): void {
                $code = 'PETBD-'.$pet->id.'-'.now()->year;
                $coupon = Coupon::query()->firstOrCreate(
                    ['code' => $code],
                    [
                        'user_id' => $pet->user_id,
                        'description' => 'Ưu đãi sinh nhật cho '.$pet->name,
                        'type' => CouponType::Percent,
                        'value' => 10,
                        'max_discount' => 100000,
                        'expires_at' => now()->endOfDay(),
                        'is_active' => true,
                    ],
                );

                if ($coupon->wasRecentlyCreated) {
                    $coupon->user_id = $pet->user_id;
                    $coupon->save();
                    $pet->user->notify(new PetBirthdayNotification($pet, $coupon));
                    $created++;
                }
            });

        $this->info("Created {$created} birthday coupon(s).");

        return self::SUCCESS;
    }
}
