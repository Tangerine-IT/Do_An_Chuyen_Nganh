<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Service;
use App\Models\Staff;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * @return Collection<int, Carbon>
     */
    public function slots(Service $service, CarbonInterface $date, ?Staff $staff = null): Collection
    {
        $day = Carbon::parse($date)->startOfDay();
        $hours = $this->hoursFor($day, $staff);
        if (! $hours || $day->isBefore(today())) {
            return collect();
        }

        $open = $day->copy()->setTimeFromTimeString($hours['open']);
        $close = $day->copy()->setTimeFromTimeString($hours['close']);
        $duration = $service->duration_minutes + (int) config('petshop.booking.buffer_minutes');
        $booked = $staff
            ? $staff->bookings()
                ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
                ->where('starts_at', '<', $close)
                ->where('ends_at', '>', $open)
                ->get(['starts_at', 'ends_at'])
            : collect();

        $slots = collect();
        for ($start = $open->copy(); $start->copy()->addMinutes($duration)->lte($close); $start->addMinutes((int) config('petshop.booking.slot_minutes'))) {
            $end = $start->copy()->addMinutes($service->duration_minutes);
            if ($start->isFuture() && ! $booked->contains(fn ($booking): bool => $start->lt($booking->ends_at) && $end->gt($booking->starts_at))) {
                $slots->push($start->copy());
            }
        }

        return $slots;
    }

    /** @return Collection<int, Staff> */
    public function staffFor(Service $service): Collection
    {
        $assigned = $service->staff()->where('is_active', true)->get();

        return $assigned->isNotEmpty() ? $assigned : Staff::query()->active()->orderBy('name')->get();
    }

    /** @return array{open: string, close: string}|null */
    private function hoursFor(Carbon $day, ?Staff $staff): ?array
    {
        $schedule = $staff?->schedules()->where('weekday', $day->isoWeekday())->first();
        if ($schedule) {
            return $schedule->is_day_off ? null : [
                'open' => $schedule->open_time,
                'close' => $schedule->close_time,
            ];
        }

        return config('petshop.working_hours.'.$day->isoWeekday());
    }
}
