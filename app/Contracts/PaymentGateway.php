<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentGateway
{
    public function createPaymentUrl(Order $order): string;

    /** @param array<string, string> $payload */
    public function verify(array $payload): bool;

    /** @param array<string, string> $payload */
    public function transactionId(array $payload): string;

    /** @param array<string, string> $payload */
    public function isSuccessful(array $payload): bool;

    /** @param array<string, string> $payload */
    public function amount(array $payload): int;
}
