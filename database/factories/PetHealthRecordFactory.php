<?php

namespace Database\Factories;

use App\Enums\HealthRecordType;
use App\Models\Pet;
use App\Models\PetHealthRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PetHealthRecord>
 */
class PetHealthRecordFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pet_id' => Pet::factory(),
            'type' => HealthRecordType::Checkup,
            'record_date' => fake()->dateTimeBetween('-1 year', '-1 week'),
            'next_due_date' => null,
            'weight_kg' => null,
            'note' => null,
            'reminded_at' => null,
        ];
    }

    public function vaccination(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => HealthRecordType::Vaccination,
            'note' => 'Tiêm mũi nhắc lại theo lịch.',
        ]);
    }

    public function dueInDays(int $days): static
    {
        return $this->state(fn (array $attributes) => [
            'next_due_date' => now()->addDays($days)->toDateString(),
        ]);
    }

    public function reminded(): static
    {
        return $this->state(fn (array $attributes) => ['reminded_at' => now()]);
    }
}
