<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(2, true)).' Pet';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'logo_path' => null,
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
