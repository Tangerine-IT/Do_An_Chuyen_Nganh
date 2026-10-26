<?php

namespace Tests\Feature\Models;

use App\Enums\HealthRecordType;
use App\Enums\PetSex;
use App\Enums\PetSpecies;
use App\Models\Booking;
use App\Models\Pet;
use App\Models\PetHealthRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PetModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pet_attributes_are_cast(): void
    {
        $pet = Pet::factory()->create([
            'species' => PetSpecies::Dog,
            'sex' => PetSex::Female,
            'birthday' => '2022-03-10',
            'weight_kg' => 12.5,
            'allergies' => ['gà', 'ngũ cốc'],
        ])->fresh();

        $this->assertSame(PetSpecies::Dog, $pet->species);
        $this->assertSame(PetSex::Female, $pet->sex);
        $this->assertInstanceOf(Carbon::class, $pet->birthday);
        $this->assertSame('12.50', $pet->weight_kg);
        $this->assertSame(['gà', 'ngũ cốc'], $pet->allergies);
    }

    public function test_pet_belongs_to_a_user_through_the_users_relationship(): void
    {
        $user = User::factory()->create();

        $pet = $user->pets()->create([
            'name' => 'Bông',
            'species' => PetSpecies::Dog,
        ]);

        $this->assertSame($user->id, $pet->user_id);
        $this->assertTrue($pet->user->is($user));
    }

    public function test_owner_cannot_be_mass_assigned(): void
    {
        $pet = new Pet(['name' => 'Bông', 'user_id' => 999]);

        $this->assertNull($pet->user_id);
    }

    public function test_pet_has_many_health_records_and_bookings(): void
    {
        $pet = Pet::factory()->create();
        PetHealthRecord::factory()->count(3)->for($pet)->create();
        Booking::factory()->create(['user_id' => $pet->user_id, 'pet_id' => $pet->id]);

        $this->assertCount(3, $pet->healthRecords);
        $this->assertCount(1, $pet->bookings);
        $this->assertTrue($pet->healthRecords->first()->pet->is($pet));
    }

    public function test_health_record_attributes_are_cast(): void
    {
        $record = PetHealthRecord::factory()->vaccination()->create([
            'record_date' => '2026-01-10',
            'next_due_date' => '2027-01-10',
            'weight_kg' => 8.25,
        ])->fresh();

        $this->assertSame(HealthRecordType::Vaccination, $record->type);
        $this->assertInstanceOf(Carbon::class, $record->record_date);
        $this->assertSame('2027-01-10', $record->next_due_date->toDateString());
        $this->assertSame('8.25', $record->weight_kg);
        $this->assertNull($record->reminded_at);
    }

    public function test_reminder_timestamp_is_cast_and_not_mass_assignable(): void
    {
        $record = new PetHealthRecord(['note' => 'x', 'reminded_at' => now()]);

        $this->assertNull($record->reminded_at);

        $reminded = PetHealthRecord::factory()->reminded()->create();

        $this->assertInstanceOf(Carbon::class, $reminded->fresh()->reminded_at);
    }

    public function test_deleting_a_pet_removes_its_health_records(): void
    {
        $pet = Pet::factory()->create();
        PetHealthRecord::factory()->count(2)->for($pet)->create();

        $pet->delete();

        $this->assertSame(0, PetHealthRecord::count());
    }
}
