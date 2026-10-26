<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingService
{
    public function __construct(private readonly AvailabilityService $availability) {}

    public function create(User $user, Pet $pet, Service $service, Staff $staff, CarbonInterface $startsAt, ?string $notes = null): Booking
    {
        if (! $pet->user()->whereKey($user->id)->exists()) {
            abort(403);
        }

        return DB::transaction(function () use ($user, $pet, $service, $staff, $startsAt, $notes): Booking {
            $startsAt = $startsAt->copy();
            if ($startsAt->isAfter(now()->addDays((int) config('petshop.booking.max_days_ahead')))) {
                throw new RuntimeException(__('ui.booking.too_far_ahead'));
            }
            $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);
            $conflict = Booking::query()
                ->where('staff_id', $staff->id)
                ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->lockForUpdate()
                ->exists();

            if ($conflict || ! $this->availability->slots($service, $startsAt, $staff)->contains(fn ($slot): bool => $slot->equalTo($startsAt))) {
                throw new RuntimeException(__('ui.booking.slot_unavailable'));
            }

            return $user->bookings()->create([
                'pet_id' => $pet->id,
                'service_id' => $service->id,
                'staff_id' => $staff->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => BookingStatus::Pending,
                'price' => $service->price,
                'notes' => $notes,
            ]);
        });
    }

    public function cancel(User $user, Booking $booking): void
    {
        if (! $booking->user()->whereKey($user->id)->exists()) {
            abort(403);
        }
        if ($booking->starts_at->lessThanOrEqualTo(now()->addHours((int) config('petshop.booking.cancel_before_hours')))) {
            throw new RuntimeException(__('ui.booking.cancel_too_late'));
        }

        $booking->update(['status' => BookingStatus::Cancelled]);
    }

    public function reschedule(User $user, Booking $booking, CarbonInterface $startsAt): Booking
    {
        if (! $booking->user()->whereKey($user->id)->exists()) {
            abort(403);
        }
        if ($booking->starts_at->lessThanOrEqualTo(now()->addHours((int) config('petshop.booking.cancel_before_hours')))) {
            throw new RuntimeException(__('ui.booking.reschedule_too_late'));
        }

        return DB::transaction(function () use ($booking, $startsAt): Booking {
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            if ($startsAt->isAfter(now()->addDays((int) config('petshop.booking.max_days_ahead')))) {
                throw new RuntimeException(__('ui.booking.too_far_ahead'));
            }
            $endsAt = $startsAt->copy()->addMinutes($booking->service->duration_minutes);
            $conflict = Booking::query()
                ->where('id', '!=', $booking->id)
                ->where('staff_id', $booking->staff_id)
                ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->lockForUpdate()
                ->exists();

            if ($conflict || ! $this->availability->slots($booking->service, $startsAt, $booking->staff)->contains(fn ($slot): bool => $slot->equalTo($startsAt))) {
                throw new RuntimeException(__('ui.booking.slot_unavailable'));
            }

            $booking->update(['starts_at' => $startsAt, 'ends_at' => $endsAt, 'status' => BookingStatus::Pending]);

            return $booking->fresh();
        });
    }
}
