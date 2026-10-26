<?php

namespace Database\Factories;

use App\Enums\AdoptionStatus;
use App\Enums\PetSex;
use App\Enums\PetSpecies;
use App\Models\AdoptionListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdoptionListing>
 */
class AdoptionListingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Vàng', 'Mực', 'Tom', 'Bơ', 'Cốm', 'Lucky']),
            'species' => fake()->randomElement([PetSpecies::Dog, PetSpecies::Cat]),
            'breed' => null,
            'sex' => fake()->randomElement(PetSex::cases()),
            'age_months' => fake()->numberBetween(2, 48),
            'description' => 'Bé hiền, thân thiện với người, đang tìm một gia đình yêu thương.',
            'is_vaccinated' => fake()->boolean(),
            'is_neutered' => fake()->boolean(),
            'photo_path' => null,
            'contact_name' => fake()->name(),
            'contact_phone' => fake()->numerify('09########'),
            'city' => 'TP. Hồ Chí Minh',
            'latitude' => 10.8231,
            'longitude' => 106.6297,
            'status' => AdoptionStatus::Available,
        ];
    }

    public function adopted(): static
    {
        return $this->state(fn (array $attributes) => ['status' => AdoptionStatus::Adopted]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => AdoptionStatus::Pending]);
    }
}
