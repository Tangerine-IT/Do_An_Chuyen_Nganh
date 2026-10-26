<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'Bài viết '.fake()->unique()->words(4, true);

        return [
            'user_id' => User::factory()->admin(),
            'title' => $title,
            'slug' => Str::slug($title),
            'category' => fake()->randomElement(['Dinh dưỡng', 'Sức khỏe', 'Chăm sóc']),
            'excerpt' => fake()->sentence(12),
            'body' => implode("\n\n", fake()->paragraphs(3)),
            'cover_image_path' => null,
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(fake()->numberBetween(1, 60)),
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
            'published_at' => now()->addDays(3),
        ]);
    }
}
