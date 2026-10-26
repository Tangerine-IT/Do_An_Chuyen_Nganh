<?php

namespace App\Contracts;

interface AiClient
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{answer: string, product_ids: list<int>, warnings: list<string>}
     */
    public function respond(string $message, array $context): array;
}
