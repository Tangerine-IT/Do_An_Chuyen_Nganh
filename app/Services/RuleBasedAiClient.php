<?php

namespace App\Services;

use App\Contracts\AiClient;
use Illuminate\Support\Str;

class RuleBasedAiClient implements AiClient
{
    public function respond(string $message, array $context): array
    {
        $products = collect($context['products'] ?? []);
        $allergies = collect($context['pet']['allergies'] ?? [])
            ->map(fn ($allergy) => Str::lower(Str::ascii((string) $allergy)));
        $safe = $products->filter(function (array $product) use ($allergies): bool {
            $ingredients = Str::lower(Str::ascii($product['ingredients'] ?? ''));
            foreach ($allergies as $allergy) {
                $mapping = match ($allergy) {
                    'ga' => ['ga', 'chicken'],
                    'bo' => ['bo', 'beef'],
                    'ca' => ['ca', 'fish'],
                    default => [$allergy],
                };
                if (collect($mapping)->contains(fn (string $term): bool => Str::contains($ingredients, $term))) {
                    return false;
                }
            }

            return true;
        });

        $answer = $safe->isEmpty()
            ? 'Mình chưa tìm thấy sản phẩm phù hợp trong danh mục hiện tại. Bạn có thể thử mô tả rõ hơn nhu cầu hoặc hỏi bác sĩ thú y nếu bé có vấn đề sức khỏe.'
            : 'Mình tìm thấy một số sản phẩm trong danh mục có thể phù hợp. Bạn hãy kiểm tra thành phần và đối chiếu với hồ sơ dị ứng của bé trước khi mua.';

        return [
            'answer' => $answer,
            'product_ids' => $safe->pluck('id')->take(4)->map(fn ($id): int => (int) $id)->values()->all(),
            'warnings' => $allergies->isNotEmpty() ? ['Hãy kiểm tra kỹ thành phần theo dị ứng của bé trước khi sử dụng.'] : [],
        ];
    }
}
