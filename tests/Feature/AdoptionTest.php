<?php

namespace Tests\Feature;

use App\Enums\AdoptionApplicationStatus;
use App\Enums\AdoptionStatus;
use App\Models\AdoptionListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdoptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_adoption_filters_available_listings(): void
    {
        $available = AdoptionListing::factory()->create(['city' => 'Hà Nội', 'age_months' => 8]);
        AdoptionListing::factory()->create(['city' => 'Đà Nẵng']);
        AdoptionListing::factory()->create(['status' => AdoptionStatus::Adopted]);

        $this->get(route('adoption.index', ['city' => 'Hà Nội', 'age' => 'young']))
            ->assertOk()
            ->assertSee($available->name);
    }

    public function test_customer_can_apply_once_and_admin_can_approve(): void
    {
        Notification::fake();
        $customer = User::factory()->create();
        $listing = AdoptionListing::factory()->create();

        $this->actingAs($customer)->post(route('adoption.apply', $listing), [
            'phone' => '0901234567',
            'message' => 'Tôi có thể chăm sóc bé.',
        ])->assertRedirect();
        $this->assertDatabaseHas('adoption_applications', [
            'adoption_listing_id' => $listing->id,
            'user_id' => $customer->id,
            'status' => AdoptionApplicationStatus::Pending->value,
        ]);
        $this->actingAs($customer)->post(route('adoption.apply', $listing), [
            'phone' => '0901234567',
            'message' => 'Đăng ký lần nữa.',
        ])->assertSessionHasErrors('application');

        $application = $listing->applications()->firstOrFail();
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->patch(route('admin.adoption.applications.status', $application), [
            'status' => AdoptionApplicationStatus::Approved->value,
        ])->assertRedirect();
        $this->assertDatabaseHas('adoption_applications', ['id' => $application->id, 'status' => 'approved']);
        $this->assertDatabaseHas('adoption_listings', ['id' => $listing->id, 'status' => AdoptionStatus::Adopted->value]);
    }

    public function test_guest_cannot_apply_and_customer_cannot_review_applications(): void
    {
        $listing = AdoptionListing::factory()->create();
        $this->post(route('adoption.apply', $listing), [])->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('admin.adoption.applications'))->assertForbidden();
    }
}
