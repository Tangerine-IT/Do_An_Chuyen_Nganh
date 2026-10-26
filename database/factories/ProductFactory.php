<?php

namespace Database\Factories;

use App\Enums\LifeStage;
use App\Enums\ProductSpecies;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $flavor = fake()->randomElement(['Gà', 'Bò', 'Cá hồi', 'Cá ngừ', 'Vịt']);
        $name = fake()->randomElement(['Hạt khô', 'Pate', 'Bánh thưởng']).' vị '.$flavor.' '.fake()->randomElement(['500g', '1kg', '2kg']);

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'sku' => fake()->unique()->bothify('PS-######'),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'ingredients' => 'Thịt '.Str::lower($flavor).', gạo, dầu cá, vitamin và khoáng chất',
            'price' => fake()->numberBetween(20, 900) * 1000,
            'sale_price' => null,
            'stock' => fake()->numberBetween(10, 200),
            'package_size_grams' => 2000,
            'daily_grams_per_kg' => 20,
            'species' => fake()->randomElement(ProductSpecies::cases()),
            'life_stage' => fake()->randomElement([...LifeStage::cases(), null]),
            'min_pet_weight_kg' => null,
            'max_pet_weight_kg' => null,
            'is_active' => true,
            'is_featured' => false,
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    /**
     * Applies a sale price at the given percentage off the (already resolved) price.
     */
    public function onSale(int $percentOff = 15): static
    {
        return $this->state(fn (array $attributes) => [
            'sale_price' => (int) (round($attributes['price'] * (100 - $percentOff) / 100 / 1000) * 1000),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => ['stock' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }

    public function forSpecies(ProductSpecies $species): static
    {
        return $this->state(fn (array $attributes) => ['species' => $species]);
    }
}
