<?php

namespace Tests\Feature\Models;

use App\Enums\AdoptionStatus;
use App\Enums\ChatRole;
use App\Enums\LoyaltyReason;
use App\Enums\PetSpecies;
use App\Enums\PostStatus;
use App\Models\AdoptionListing;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\Pet;
use App\Models\Post;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class EngagementModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_relations_and_approved_scope(): void
    {
        $product = Product::factory()->create();
        $approved = Review::factory()->for($product)->create();
        Review::factory()->for($product)->pending()->create();

        $this->assertCount(2, $product->reviews);
        $this->assertTrue($approved->product->is($product));
        $this->assertInstanceOf(User::class, $approved->user);
        $this->assertSame([$approved->id], $product->reviews()->approved()->pluck('id')->all());
    }

    public function test_a_user_reviews_a_product_only_once(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Review::factory()->for($user)->for($product)->create();

        $this->expectException(QueryException::class);

        Review::factory()->for($user)->for($product)->create();
    }

    public function test_wishlist_row_links_user_and_product(): void
    {
        $wishlist = Wishlist::factory()->create();

        $this->assertInstanceOf(User::class, $wishlist->user);
        $this->assertInstanceOf(Product::class, $wishlist->product);
    }

    public function test_post_author_and_published_scope(): void
    {
        $author = User::factory()->admin()->create();
        $published = Post::factory()->for($author, 'author')->create();
        Post::factory()->for($author, 'author')->draft()->create();
        Post::factory()->for($author, 'author')->scheduled()->create();

        $this->assertTrue($published->author->is($author));
        $this->assertSame(PostStatus::Published, $published->status);
        $this->assertSame([$published->id], Post::published()->pluck('id')->all());
    }

    public function test_deleting_the_author_keeps_the_post(): void
    {
        $author = User::factory()->admin()->create();
        $post = Post::factory()->for($author, 'author')->create();

        $author->delete();

        $this->assertNull($post->fresh()->user_id);
    }

    public function test_adoption_listing_attributes_and_available_scope(): void
    {
        $poster = User::factory()->create();
        $available = AdoptionListing::factory()->for($poster)->create([
            'species' => PetSpecies::Cat,
            'latitude' => 21.0285,
            'longitude' => 105.8542,
        ]);
        AdoptionListing::factory()->adopted()->create();
        AdoptionListing::factory()->pending()->create();

        $fresh = $available->fresh();

        $this->assertTrue($fresh->user->is($poster));
        $this->assertSame(PetSpecies::Cat, $fresh->species);
        $this->assertSame(AdoptionStatus::Available, $fresh->status);
        $this->assertSame('21.0285000', $fresh->latitude);
        $this->assertSame([$available->id], AdoptionListing::available()->pluck('id')->all());
    }

    public function test_adoption_status_is_not_mass_assignable(): void
    {
        $listing = new AdoptionListing(['name' => 'Vàng', 'status' => 'adopted']);

        $this->assertNull($listing->status);
    }

    public function test_loyalty_ledger_records_signed_points_and_the_resulting_balance(): void
    {
        $user = User::factory()->withLoyaltyPoints(70)->create();
        $order = Order::factory()->for($user)->create();

        $entry = $user->loyaltyTransactions()->create([
            'order_id' => $order->id,
            'points' => -30,
            'balance_after' => 70,
            'reason' => LoyaltyReason::Redeemed,
        ])->fresh();

        $this->assertSame(-30, $entry->points);
        $this->assertSame(LoyaltyReason::Redeemed, $entry->reason);
        $this->assertTrue($entry->user->is($user));
        $this->assertTrue($entry->order->is($order));
        $this->assertInstanceOf(Carbon::class, $entry->created_at);
        $this->assertArrayNotHasKey('updated_at', $entry->getAttributes());
    }

    public function test_chat_messages_group_by_conversation_and_reference_a_pet(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->for($user)->create();
        $conversation = (string) Str::uuid();

        $question = ChatMessage::factory()->for($user)->create(['pet_id' => $pet->id, 'conversation_id' => $conversation]);
        $answer = ChatMessage::factory()->assistant()->for($user)->create(['pet_id' => $pet->id, 'conversation_id' => $conversation]);
        ChatMessage::factory()->for($user)->create();

        $thread = ChatMessage::where('conversation_id', $conversation)->orderBy('id')->get();

        $this->assertSame([$question->id, $answer->id], $thread->pluck('id')->all());
        $this->assertSame(ChatRole::User, $thread[0]->role);
        $this->assertSame(ChatRole::Assistant, $thread[1]->role);
        $this->assertTrue($thread[0]->pet->is($pet));
        $this->assertTrue($thread[0]->user->is($user));
    }

    public function test_deleting_a_pet_keeps_the_chat_history(): void
    {
        $pet = Pet::factory()->create();
        $message = ChatMessage::factory()->create(['pet_id' => $pet->id, 'user_id' => $pet->user_id]);

        $pet->delete();

        $this->assertNull($message->fresh()->pet_id);
    }
}
