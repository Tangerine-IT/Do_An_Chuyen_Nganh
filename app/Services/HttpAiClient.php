<?php

namespace App\Services;

use App\Contracts\AiClient;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HttpAiClient implements AiClient
{
    public function respond(string $message, array $context): array
    {
        $config = config('services.ai');
        if (blank($config['api_key'] ?? null)) {
            throw new RuntimeException(__('ui.chat.not_configured'));
        }

        $response = Http::timeout((int) ($config['timeout'] ?? 15))
            ->withToken($config['api_key'])
            ->post($config['endpoint'], [
                'model' => $config['model'],
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $context['instructions'].' Trả về JSON đúng schema: {answer:string, product_ids:number[], warnings:string[]}.'],
                    ['role' => 'user', 'content' => json_encode(['question' => $message, 'context' => $context], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)],
                ],
            ])->throw();

        $content = data_get($response->json(), 'choices.0.message.content');
        $data = json_decode((string) $content, true);
        if (! is_array($data) || ! is_string($data['answer'] ?? null) || ! is_array($data['product_ids'] ?? null) || ! is_array($data['warnings'] ?? null)) {
            throw new RuntimeException(__('ui.chat.invalid_response'));
        }

        return [
            'answer' => $data['answer'],
            'product_ids' => array_values(array_filter($data['product_ids'], 'is_numeric')),
            'warnings' => array_values(array_filter($data['warnings'], 'is_string')),
        ];
    }
}
