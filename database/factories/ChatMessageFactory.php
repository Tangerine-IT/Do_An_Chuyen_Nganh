<?php

namespace Database\Factories;

use App\Enums\ChatRole;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'pet_id' => null,
            'conversation_id' => (string) Str::uuid(),
            'role' => ChatRole::User,
            'content' => fake()->randomElement([
                'Bé nhà mình bị dị ứng gà, nên chọn hạt nào?',
                'Chó con 3 tháng tuổi nên cho ăn mấy bữa một ngày?',
                'Mèo triệt sản nên ăn loại hạt nào để không béo?',
            ]),
        ];
    }

    public function assistant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => ChatRole::Assistant,
            'content' => 'Mình gợi ý bạn chọn dòng hạt không chứa gà và ít ngũ cốc nhé.',
        ]);
    }
}
