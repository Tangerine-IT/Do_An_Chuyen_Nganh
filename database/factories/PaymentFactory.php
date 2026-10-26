<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'method' => PaymentMethod::Cod,
            'status' => PaymentStatus::Pending,
            'amount' => fn (array $attributes) => Order::findOrFail($attributes['order_id'])->total,
            'transaction_id' => null,
            'gateway_response' => null,
            'paid_at' => null,
        ];
    }

    public function paidVia(PaymentMethod $method): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => $method,
            'status' => PaymentStatus::Paid,
            'transaction_id' => strtoupper(Str::random(12)),
            'paid_at' => now(),
        ]);
    }
}
