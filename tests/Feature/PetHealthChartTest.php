<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\PetHealthRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetHealthChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_pet_health_page_embeds_weight_data_for_the_chart(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        PetHealthRecord::factory()->create([
            'pet_id' => $pet->id,
            'record_date' => '2026-10-01',
            'weight_kg' => 48.5,
        ]);

        $this->actingAs($user)
            ->get(route('account.pets.show', $pet))
            ->assertOk()
            ->assertSee('id="pet-weight-chart"', false)
            ->assertSee('48.5', false)
            ->assertSee('01/10/2026', false);
    }
}
