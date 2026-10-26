<?php

namespace App\Services;

use App\Models\FeedingEstimate;
use App\Models\Pet;
use App\Models\Product;

class FeedingEstimator
{
    public function estimate(Pet $pet, Product $product): FeedingEstimate
    {
        $daily = max(1, (float) $pet->weight_kg * (float) $product->daily_grams_per_kg);
        $days = max(1, (int) floor((int) $product->package_size_grams / $daily));

        return FeedingEstimate::updateOrCreate(
            ['pet_id' => $pet->id, 'product_id' => $product->id],
            [
                'package_size_grams' => $product->package_size_grams,
                'daily_grams' => $daily,
                'runs_out_at' => now()->addDays($days)->toDateString(),
                'reminded_at' => null,
            ],
        );
    }
}
