<?php

namespace Tests\Feature;

use App\Enums\ProductSpecies;
use App\Models\Pet;
use App\Models\Product;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase10Test extends TestCase
{
    use RefreshDatabase;

    public function test_recommendation_matches_vietnamese_allergy_terms_case_insensitively(): void
    {
        $pet = Pet::factory()->allergicTo(['gà'])->create();
        $product = Product::factory()->forSpecies(ProductSpecies::Dog)->create([
            'ingredients' => 'Thịt CHICKEN, gạo',
        ]);

        $explanation = app(RecommendationService::class)->explain($product, $pet);

        $this->assertSame('warning', $explanation['status']);
        $this->assertStringContainsString('gà', implode(' ', $explanation['reasons']));
    }

    public function test_recommendations_exclude_other_species(): void
    {
        $pet = Pet::factory()->cat()->create();
        $dogProduct = Product::factory()->forSpecies(ProductSpecies::Dog)->create();
        $catProduct = Product::factory()->forSpecies(ProductSpecies::Cat)->create();

        $products = app(RecommendationService::class)->recommended($pet);

        $this->assertFalse($products->contains($dogProduct));
        $this->assertTrue($products->contains($catProduct));
    }

    public function test_customer_can_select_only_owned_current_pet(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        $otherPet = Pet::factory()->create();

        $this->actingAs($user)
            ->post(route('account.pets.current', $pet))
            ->assertRedirect();
        $this->assertSame($pet->id, session('current_pet_id'));

        $this->actingAs($user)
            ->post(route('account.pets.current', $otherPet))
            ->assertForbidden();
    }
}
