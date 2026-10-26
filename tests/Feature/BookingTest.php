<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_availability_respects_working_hours_and_existing_booking(): void
    {
        $service = Service::factory()->create(['duration_minutes' => 60]);
        $staff = Staff::factory()->create();
        $date = now()->addDays(2)->setTime(0, 0);
        Booking::factory()->create([
            'service_id' => $service->id,
            'staff_id' => $staff->id,
            'starts_at' => $date->copy()->setTime(10, 0),
            'ends_at' => $date->copy()->setTime(11, 0),
        ]);

        $slots = app(AvailabilityService::class)->slots($service, $date, $staff);

        $this->assertFalse($slots->contains(fn (Carbon $slot): bool => $slot->format('H:i') === '10:00'));
        $this->assertTrue($slots->contains(fn (Carbon $slot): bool => $slot->format('H:i') === '11:00'));
    }

    public function test_booking_creation_rejects_overlapping_staff_slot(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->for($user)->create();
        $service = Service::factory()->create(['duration_minutes' => 60]);
        $staff = Staff::factory()->create();
        $start = now()->addDays(3)->setTime(10, 0);
        Booking::factory()->create([
            'user_id' => $user->id,
            'pet_id' => $pet->id,
            'service_id' => $service->id,
            'staff_id' => $staff->id,
            'starts_at' => $start,
            'ends_at' => $start->copy()->addHour(),
        ]);

        $this->expectException(RuntimeException::class);
        app(BookingService::class)->create($user, $pet, $service, $staff, $start);
    }

    public function test_booking_creation_rejects_dates_beyond_the_booking_horizon(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->for($user)->create();
        $service = Service::factory()->create();
        $staff = Staff::factory()->create();
        $start = now()->addDays((int) config('petshop.booking.max_days_ahead') + 1)->setTime(10, 0);

        $this->expectException(RuntimeException::class);
        app(BookingService::class)->create($user, $pet, $service, $staff, $start);
    }

    public function test_customer_can_create_and_cancel_a_future_booking(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->for($user)->create();
        $service = Service::factory()->create(['duration_minutes' => 30]);
        $staff = Staff::factory()->create();
        $start = now()->addDays(4)->setTime(10, 0);

        $booking = app(BookingService::class)->create($user, $pet, $service, $staff, $start);
        app(BookingService::class)->cancel($user, $booking);

        $this->assertSame(BookingStatus::Cancelled, $booking->fresh()->status);
    }

    public function test_booking_routes_and_admin_calendar_are_authorized(): void
    {
        $customer = User::factory()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($customer)->get(route('bookings.index'))->assertOk();
        $this->actingAs($staff)->get(route('admin.bookings.index'))->assertOk();
        $this->actingAs($customer)->get(route('admin.bookings.index'))->assertForbidden();
    }

    public function test_staff_day_off_removes_availability(): void
    {
        $service = Service::factory()->create(['duration_minutes' => 60]);
        $staff = Staff::factory()->create();
        $date = now()->addDays(2)->setTime(0, 0);
        $staff->schedules()->create([
            'weekday' => $date->isoWeekday(),
            'is_day_off' => true,
        ]);

        $this->assertCount(0, app(AvailabilityService::class)->slots($service, $date, $staff));
    }

    public function test_customer_can_reschedule_a_future_booking(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->for($user)->create();
        $service = Service::factory()->create(['duration_minutes' => 30]);
        $staff = Staff::factory()->create();
        $booking = app(BookingService::class)->create($user, $pet, $service, $staff, now()->addDays(4)->setTime(10, 0));
        $newStart = now()->addDays(5)->setTime(11, 0);

        app(BookingService::class)->reschedule($user, $booking, $newStart);

        $this->assertTrue($booking->fresh()->starts_at->equalTo($newStart));
    }

    public function test_staff_can_create_a_manual_booking_for_a_customer(): void
    {
        $staffUser = User::factory()->staff()->create();
        $customer = User::factory()->create();
        $pet = Pet::factory()->for($customer)->create();
        $service = Service::factory()->create(['duration_minutes' => 30]);
        $provider = Staff::factory()->create();

        $this->actingAs($staffUser)->get(route('admin.bookings.create'))->assertOk();
        $this->actingAs($staffUser)->post(route('admin.bookings.store'), [
            'user_id' => $customer->id,
            'pet_id' => $pet->id,
            'service_id' => $service->id,
            'staff_id' => $provider->id,
            'starts_at' => now()->addDays(3)->setTime(10, 0)->toDateTimeString(),
        ])->assertRedirect(route('admin.bookings.index'));

        $this->assertDatabaseHas('bookings', ['user_id' => $customer->id, 'pet_id' => $pet->id]);
    }
}
