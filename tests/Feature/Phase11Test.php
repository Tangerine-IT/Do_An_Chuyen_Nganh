<?php

namespace Tests\Feature;

use App\Enums\HealthRecordType;
use App\Models\Pet;
use App\Models\PetHealthRecord;
use App\Models\User;
use App\Notifications\PetBirthdayNotification;
use App\Notifications\PetHealthReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class Phase11Test extends TestCase
{
    use RefreshDatabase;

    public function test_health_reminder_selects_due_and_overdue_records_once(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        $due = PetHealthRecord::factory()->dueInDays(3)->create(['pet_id' => $pet->id]);
        $overdue = PetHealthRecord::factory()->dueInDays(-2)->create(['pet_id' => $pet->id]);
        PetHealthRecord::factory()->dueInDays(3)->reminded()->create(['pet_id' => $pet->id]);
        PetHealthRecord::factory()->dueInDays(10)->create(['pet_id' => $pet->id]);

        Artisan::call('pets:send-health-reminders');
        Artisan::call('pets:send-health-reminders');

        Notification::assertSentTo($user, PetHealthReminderNotification::class, 2);
        $this->assertNotNull($due->fresh()->reminded_at);
        $this->assertNotNull($overdue->fresh()->reminded_at);
    }

    public function test_customer_can_manage_owned_health_records_only(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $owner->id]);
        $otherPet = Pet::factory()->create(['user_id' => $other->id]);

        $this->actingAs($owner)
            ->post(route('account.pets.health.store', $pet), [
                'type' => HealthRecordType::Vaccination->value,
                'record_date' => now()->toDateString(),
                'next_due_date' => now()->addDays(30)->toDateString(),
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('pet_health_records', ['pet_id' => $pet->id, 'type' => 'vaccination']);

        $this->actingAs($owner)->get(route('account.pets.show', $otherPet))->assertForbidden();
    }

    public function test_public_pet_page_respects_contact_privacy_and_qr_is_available(): void
    {
        $user = User::factory()->create(['phone' => '0900000000']);
        $pet = Pet::factory()->create([
            'user_id' => $user->id,
            'public_token' => 'public-token-for-test',
            'public_contact' => false,
        ]);

        $this->get(route('pets.public', $pet->public_token))
            ->assertOk()
            ->assertDontSee($user->phone);
        $this->get(route('pets.qr', $pet->public_token))
            ->assertOk()
            ->assertHeader('content-type', 'image/svg+xml');

        $this->actingAs($user)
            ->patch(route('account.pets.sharing.update', $pet), ['public_contact' => true])
            ->assertRedirect();
        $this->get(route('pets.public', $pet->public_token))->assertSee($user->phone);
    }

    public function test_birthday_command_is_idempotent_and_owner_scoped(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $pet = Pet::factory()->create([
            'user_id' => $user->id,
            'birthday' => now()->subYears(2)->toDateString(),
        ]);

        Artisan::call('pets:send-birthday-coupons');
        Artisan::call('pets:send-birthday-coupons');

        $this->assertDatabaseCount('coupons', 1);
        $this->assertDatabaseHas('coupons', ['user_id' => $user->id, 'code' => 'PETBD-'.$pet->id.'-'.now()->year]);
        Notification::assertSentTo($user, PetBirthdayNotification::class, 1);
    }
}
