<?php

namespace Database\Factories;

use App\Enums\PetSex;
use App\Enums\PetSpecies;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pet>
 */
class PetFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Bông', 'Mít', 'Lu', 'Mun', 'Kem', 'Sữa', 'Cốm', 'Bơ', 'Xoài', 'Tôm', 'Gấu', 'Mochi']),
            'species' => PetSpecies::Dog,
            'breed' => fake()->randomElement(['Poodle', 'Corgi', 'Golden Retriever', 'Pug', 'Phốc sóc']),
            'birthday' => fake()->dateTimeBetween('-10 years', '-4 months'),
            'weight_kg' => fake()->randomFloat(2, 3, 30),
            'sex' => fake()->randomElement(PetSex::cases()),
            'allergies' => [],
            'photo_path' => null,
            'notes' => null,
        ];
    }

    public function dog(): static
    {
        return $this->state(fn (array $attributes) => [
            'species' => PetSpecies::Dog,
            'breed' => fake()->randomElement(['Poodle', 'Corgi', 'Golden Retriever', 'Pug', 'Phốc sóc']),
        ]);
    }

    public function cat(): static
    {
        return $this->state(fn (array $attributes) => [
            'species' => PetSpecies::Cat,
            'breed' => fake()->randomElement(['Mèo Anh lông ngắn', 'Mèo ta', 'Mèo Ba Tư', 'Mèo Munchkin']),
            'weight_kg' => fake()->randomFloat(2, 2, 7),
        ]);
    }

    /**
     * @param  list<string>  $allergies
     */
    public function allergicTo(array $allergies): static
    {
        return $this->state(fn (array $attributes) => ['allergies' => $allergies]);
    }
}
