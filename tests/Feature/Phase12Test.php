<?php

namespace Tests\Feature;

use App\Contracts\AiClient;
use App\Enums\ProductSpecies;
use App\Livewire\ChatWidget;
use App\Models\Pet;
use App\Models\Product;
use App\Models\User;
use App\Services\ChatContextBuilder;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class Phase12Test extends TestCase
{
    use RefreshDatabase;

    public function test_context_contains_current_pet_and_only_compatible_products(): void
    {
        $user = User::factory()->create(['email' => 'private@example.test']);
        $pet = Pet::factory()->cat()->create([
            'user_id' => $user->id,
            'name' => 'Mít',
            'allergies' => ['gà'],
        ]);
        $cat = Product::factory()->forSpecies(ProductSpecies::Cat)->create(['name' => 'Pate cá thức ăn']);
        $dog = Product::factory()->forSpecies(ProductSpecies::Dog)->create(['name' => 'Hạt chó thức ăn']);

        $context = app(ChatContextBuilder::class)->build('thức ăn', $user, $pet);

        $this->assertSame('Mít', $context['pet']['name'] ?? null);
        $this->assertTrue(collect($context['products'])->pluck('id')->contains($cat->id));
        $this->assertFalse(collect($context['products'])->pluck('id')->contains($dog->id));
        $this->assertStringNotContainsString($user->email, json_encode($context));
    }

    public function test_chat_service_validates_product_ids_against_catalog(): void
    {
        $product = Product::factory()->create(['name' => 'Sản phẩm gợi ý']);
        $this->app->instance(AiClient::class, new class implements AiClient
        {
            public function respond(string $message, array $context): array
            {
                return [
                    'answer' => 'Đây là câu trả lời có cấu trúc.',
                    'product_ids' => [$context['products'][0]['id'], 999999],
                    'warnings' => ['Hãy kiểm tra dị ứng.'],
                ];
            }
        });

        $answer = app(ChatService::class)->answer('gợi ý sản phẩm', null, null);

        $this->assertSame('Đây là câu trả lời có cấu trúc.', $answer['answer']);
        $this->assertTrue($answer['products']->contains($product));
        $this->assertCount(1, $answer['products']);
        $this->assertSame(['Hãy kiểm tra dị ứng.'], $answer['warnings']);
    }

    public function test_public_layout_contains_chat_widget_without_exposing_api_key(): void
    {
        config(['services.ai.api_key' => 'server-secret']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Trợ lý PetShop')
            ->assertDontSee('server-secret');
    }

    public function test_logged_in_chat_saves_messages_and_rate_limit_is_enforced(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->app->instance(AiClient::class, new class implements AiClient
        {
            public function respond(string $message, array $context): array
            {
                return ['answer' => 'Đã nhận câu hỏi.', 'product_ids' => [], 'warnings' => []];
            }
        });

        Livewire::test(ChatWidget::class)
            ->set('message', 'Tư vấn giúp tôi')
            ->call('send')
            ->assertSet('message', '');

        $this->assertDatabaseHas('chat_messages', ['user_id' => $user->id, 'role' => 'user', 'content' => 'Tư vấn giúp tôi']);
        $this->assertDatabaseHas('chat_messages', ['user_id' => $user->id, 'role' => 'assistant', 'content' => 'Đã nhận câu hỏi.']);

        RateLimiter::clear('chat:'.$this->app['request']->ip().':'.$user->id);
    }
}
