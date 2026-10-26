<?php

namespace App\Services;

use App\Enums\ProductSpecies;
use App\Models\Pet;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RecommendationService
{
    /** @var array<string, list<string>> */
    private const ALLERGY_KEYWORDS = [
        'chicken' => ['chicken', 'gà', 'thịt gà'],
        'beef' => ['beef', 'bò', 'thịt bò'],
        'fish' => ['fish', 'cá', 'salmon', 'cá hồi'],
        'grains' => ['grain', 'grains', 'ngũ cốc', 'lúa mì', 'wheat', 'corn', 'ngô'],
        'dairy' => ['dairy', 'sữa', 'milk', 'lactose'],
        'soy' => ['soy', 'đậu nành'],
    ];

    /** @return array{status: string, reasons: list<string>, score: int} */
    public function explain(Product $product, Pet $pet): array
    {
        $reasons = [];
        $score = 0;
        $species = $pet->species?->value;

        if ($species && $product->species->value !== ProductSpecies::All->value) {
            if ($product->species->value !== $species) {
                return [
                    'status' => 'warning',
                    'reasons' => [__('ui.recommendation.other_species', ['pet' => $pet->name])],
                    'score' => -100,
                ];
            }
            $reasons[] = __('ui.recommendation.species_match', ['species' => $pet->species->label()]);
            $score += 4;
        } elseif ($species) {
            $reasons[] = __('ui.recommendation.all_species');
            $score += 2;
        }

        if ($pet->lifeStage() && $product->life_stage && $product->life_stage->value !== 'all') {
            if ($product->life_stage !== $pet->lifeStage()) {
                $reasons[] = __('ui.recommendation.life_stage_mismatch');
                $score -= 2;
            } else {
                $reasons[] = __('ui.recommendation.life_stage_match', ['stage' => $pet->lifeStage()->label()]);
                $score += 2;
            }
        }

        if ($pet->weight_kg !== null && ($product->min_pet_weight_kg !== null || $product->max_pet_weight_kg !== null)) {
            $weight = (float) $pet->weight_kg;
            $min = $product->min_pet_weight_kg === null || $weight >= (float) $product->min_pet_weight_kg;
            $max = $product->max_pet_weight_kg === null || $weight <= (float) $product->max_pet_weight_kg;
            if ($min && $max) {
                $reasons[] = __('ui.recommendation.weight_match');
                $score++;
            } else {
                $reasons[] = __('ui.recommendation.weight_mismatch');
                $score--;
            }
        }

        $ingredients = Str::lower(Str::ascii((string) $product->ingredients));
        foreach ($pet->allergies ?? [] as $allergy) {
            $key = Str::lower(Str::ascii((string) $allergy));
            $keywords = self::ALLERGY_KEYWORDS[$key] ?? [$key];
            foreach ($keywords as $keyword) {
                if (Str::contains($ingredients, Str::lower(Str::ascii($keyword)))) {
                    $reasons[] = __('ui.recommendation.allergy_warning', ['ingredient' => $allergy, 'pet' => $pet->name]);
                    $score -= 10;
                    break;
                }
            }
        }

        if ($reasons === []) {
            $reasons[] = __('ui.recommendation.incomplete');
        }

        return [
            'status' => $score < 0 ? 'warning' : ($score >= 3 ? 'recommended' : 'neutral'),
            'reasons' => $reasons,
            'score' => $score,
        ];
    }

    /** @return Collection<int, Product> */
    public function recommended(Pet $pet, int $limit = 8, bool $safeOnly = false): Collection
    {
        $products = Product::query()->active()->inStock()->with(['primaryImage', 'brand'])->get();
        if ($pet->species === null) {
            return $products->take($limit);
        }

        return $products
            ->filter(function (Product $product) use ($pet, $safeOnly): bool {
                $explanation = $this->explain($product, $pet);
                $compatibleSpecies = $product->species->value === ProductSpecies::All->value
                    || $product->species->value === $pet->species->value;

                return $compatibleSpecies && (! $safeOnly || $explanation['status'] !== 'warning');
            })
            ->map(function (Product $product) use ($pet): Product {
                $product->recommendation = $this->explain($product, $pet);

                return $product;
            })
            ->sortByDesc(fn (Product $product): int => $product->recommendation['score'])
            ->take($limit)
            ->values();
    }

    /** @return array<string, list<string>> */
    public function allergyKeywords(): array
    {
        return self::ALLERGY_KEYWORDS;
    }
}
