<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * The pet always belongs to the booking's user; ends_at and price follow the service.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'pet_id' => fn (array $attributes) => Pet::factory()->create(['user_id' => $attributes['user_id']])->id,
            'service_id' => Service::factory(),
            'staff_id' => Staff::factory(),
            'starts_at' => now()->addDays(fake()->numberBetween(1, 14))->setTime(fake()->numberBetween(8, 16), 0),
            'ends_at' => fn (array $attributes) => Carbon::parse($attributes['starts_at'])
                ->addMinutes(Service::findOrFail($attributes['service_id'])->duration_minutes),
            'status' => BookingStatus::Pending,
            'price' => fn (array $attributes) => Service::findOrFail($attributes['service_id'])->price,
            'notes' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => BookingStatus::Confirmed]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => BookingStatus::Cancelled]);
    }
}
