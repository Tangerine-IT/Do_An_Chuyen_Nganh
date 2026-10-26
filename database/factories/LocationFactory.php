<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Location> */
class LocationFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Pet Care',
            'type' => fake()->randomElement(['shop', 'vet', 'pet_friendly']),
            'description' => 'Địa điểm thân thiện với thú cưng.',
            'address' => fake()->address(),
            'phone' => fake()->numerify('09########'),
            'latitude' => 10.8231,
            'longitude' => 106.6297,
            'is_active' => true,
        ];
    }
}
