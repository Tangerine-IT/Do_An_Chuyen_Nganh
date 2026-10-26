<?php

namespace App\Services;

use App\Contracts\AiClient;
use App\Models\Pet;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class ChatService
{
    public function __construct(
        private readonly ChatContextBuilder $contextBuilder,
        private readonly AiClient $aiClient,
    ) {}

    /** @return array{answer: string, warnings: list<string>, products: Collection<int, Product>, context_pet_id: ?int} */
    public function answer(string $message, ?User $user, ?Pet $pet): array
    {
        $context = $this->contextBuilder->build($message, $user, $pet);
        try {
            $response = $this->aiClient->respond($message, $context);
        } catch (Throwable $exception) {
            report($exception);

            return [
                'answer' => __('ui.chat.error'),
                'warnings' => [],
                'products' => collect(),
                'context_pet_id' => $pet?->id,
            ];
        }

        $ids = collect($response['product_ids'])->map(fn ($id): int => (int) $id)->unique()->values();
        $products = Product::query()->active()->inStock()->whereKey($ids)->with('primaryImage')->get()
            ->sortBy(fn (Product $product): int => $ids->search($product->id))
            ->values();

        return [
            'answer' => Str::limit($response['answer'], 3000),
            'warnings' => array_values(array_unique($response['warnings'])),
            'products' => $products,
            'context_pet_id' => $pet?->id,
        ];
    }
}
