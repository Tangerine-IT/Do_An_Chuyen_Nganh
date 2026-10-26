<?php

namespace Tests\Feature\Models;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_and_staff_are_linked_many_to_many(): void
    {
        $groomer = Staff::factory()->create();
        $spa = Service::factory()->create();
        $cut = Service::factory()->create();

        $groomer->services()->attach([$spa->id, $cut->id]);

        $this->assertCount(2, $groomer->services);
        $this->assertTrue($spa->staff->first()->is($groomer));
    }

    public function test_a_staff_member_does_not_need_a_login_account(): void
    {
        $provider = Staff::factory()->create();

        $this->assertNull($provider->user_id);
        $this->assertNull($provider->user);
    }

    public function test_a_user_account_can_be_linked_to_only_one_staff_member(): void
    {
        $user = User::factory()->staff()->create();
        Staff::factory()->create(['user_id' => $user->id]);

        $this->expectException(QueryException::class);

        Staff::factory()->create(['user_id' => $user->id]);
    }

    public function test_deleting_the_linked_user_keeps_the_staff_member(): void
    {
        $user = User::factory()->staff()->create();
        $provider = Staff::factory()->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertNull($provider->fresh()->user_id);
    }

    public function test_booking_relations_and_derived_values(): void
    {
        $service = Service::factory()->create(['duration_minutes' => 90, 'price' => 250000]);
        $booking = Booking::factory()->create(['service_id' => $service->id]);

        $this->assertTrue($booking->user->is(User::first()));
        $this->assertTrue($booking->pet->user->is($booking->user), 'The pet must belong to the booking user.');
        $this->assertTrue($booking->service->is($service));
        $this->assertInstanceOf(Staff::class, $booking->staff);
        $this->assertSame(90, (int) $booking->starts_at->diffInMinutes($booking->ends_at));
        $this->assertSame(250000, $booking->price);
    }

    public function test_booking_attributes_are_cast(): void
    {
        $booking = Booking::factory()->confirmed()->create()->fresh();

        $this->assertSame(BookingStatus::Confirmed, $booking->status);
        $this->assertInstanceOf(Carbon::class, $booking->starts_at);
        $this->assertInstanceOf(Carbon::class, $booking->ends_at);
        $this->assertTrue($booking->ends_at->greaterThan($booking->starts_at));
    }

    public function test_staff_and_service_expose_their_bookings(): void
    {
        $booking = Booking::factory()->create();

        $this->assertTrue($booking->staff->bookings->first()->is($booking));
        $this->assertTrue($booking->service->bookings->first()->is($booking));
    }

    public function test_a_service_with_bookings_cannot_be_deleted(): void
    {
        $booking = Booking::factory()->create();

        $this->expectException(QueryException::class);

        $booking->service->delete();
    }

    public function test_a_staff_member_with_bookings_cannot_be_deleted(): void
    {
        $booking = Booking::factory()->create();

        $this->expectException(QueryException::class);

        $booking->staff->delete();
    }

    public function test_active_scopes_hide_deactivated_services_and_staff(): void
    {
        $service = Service::factory()->create();
        Service::factory()->inactive()->create();
        $provider = Staff::factory()->create();
        Staff::factory()->inactive()->create();

        $this->assertSame([$service->id], Service::active()->pluck('id')->all());
        $this->assertSame([$provider->id], Staff::active()->pluck('id')->all());
    }
}
