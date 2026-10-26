<?php

namespace Tests\Feature\Models;

use App\Enums\UserRole;
use App\Models\AdoptionListing;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\ChatMessage;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\Pet;
use App\Models\Post;
use App\Models\Product;
use App\Models\Review;
use App\Models\Staff;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_defaults_to_customer_role_and_zero_points(): void
    {
        DB::table('users')->insert([
            'name' => 'Khách mới',
            'email' => 'new@example.com',
            'password' => 'secret',
        ]);

        $user = User::where('email', 'new@example.com')->firstOrFail();

        $this->assertSame(UserRole::Customer, $user->role);
        $this->assertSame(0, $user->loyalty_points);
        $this->assertNull($user->phone);
    }

    public function test_new_model_instance_mirrors_the_column_defaults(): void
    {
        $user = new User;

        $this->assertSame('customer', $user->getAttributes()['role']);
        $this->assertSame(0, $user->loyalty_points);
    }

    public function test_role_helpers(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $customer = User::factory()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($staff->isAdmin());
        $this->assertTrue($staff->hasRole(UserRole::Staff, UserRole::Admin));
        $this->assertFalse($customer->hasRole(UserRole::Staff, UserRole::Admin));
    }

    public function test_role_and_loyalty_points_cannot_be_mass_assigned(): void
    {
        $user = User::create([
            'name' => 'Kẻ tấn công',
            'email' => 'attacker@example.com',
            'password' => 'password',
            'phone' => '0900000000',
            'role' => 'admin',
            'loyalty_points' => 9999,
        ]);

        $user = $user->fresh();

        $this->assertSame(UserRole::Customer, $user->role);
        $this->assertSame(0, $user->loyalty_points);
        $this->assertSame('0900000000', $user->phone);
    }

    public function test_user_has_many_pets(): void
    {
        $user = User::factory()->has(Pet::factory()->count(2))->create();

        $this->assertCount(2, $user->pets);
        $this->assertTrue($user->pets->first()->user->is($user));
    }

    public function test_user_has_many_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(2)->for($user)->create();

        $this->assertCount(2, $user->orders);
    }

    public function test_user_has_one_cart(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();

        $this->assertTrue($user->cart->is($cart));
    }

    public function test_user_has_many_reviews(): void
    {
        $user = User::factory()->create();
        Review::factory()->for($user)->create();

        $this->assertCount(1, $user->reviews);
    }

    public function test_user_wishlist_products_use_the_wishlists_table(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $user->wishlistProducts()->attach($product);

        $this->assertTrue($user->wishlistProducts->first()->is($product));
        $this->assertSame(1, Wishlist::where('user_id', $user->id)->count());
    }

    public function test_a_product_can_only_be_wishlisted_once_per_user(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $user->wishlistProducts()->attach($product);

        $this->expectException(QueryException::class);

        $user->wishlistProducts()->attach($product);
    }

    public function test_user_has_many_bookings(): void
    {
        $user = User::factory()->create();
        Booking::factory()->for($user)->create();

        $this->assertCount(1, $user->bookings);
    }

    public function test_user_may_have_a_staff_profile(): void
    {
        $user = User::factory()->staff()->create();
        $profile = Staff::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->staffProfile->is($profile));
        $this->assertTrue($profile->user->is($user));
    }

    public function test_user_has_posts_adoption_listings_loyalty_ledger_and_chat_messages(): void
    {
        $user = User::factory()->create();
        Post::factory()->for($user, 'author')->create();
        AdoptionListing::factory()->for($user)->create();
        LoyaltyPoint::factory()->for($user)->create();
        ChatMessage::factory()->for($user)->create();

        $this->assertCount(1, $user->posts);
        $this->assertCount(1, $user->adoptionListings);
        $this->assertCount(1, $user->loyaltyTransactions);
        $this->assertCount(1, $user->chatMessages);
    }

    public function test_deleting_a_user_removes_their_private_data(): void
    {
        $user = User::factory()->create();
        Pet::factory()->for($user)->create();
        Cart::factory()->for($user)->create();
        Review::factory()->for($user)->create();
        Booking::factory()->for($user)->create();
        ChatMessage::factory()->for($user)->create();

        $user->delete();

        $this->assertSame(0, Pet::count());
        $this->assertSame(0, Cart::count());
        $this->assertSame(0, Review::count());
        $this->assertSame(0, Booking::count());
        $this->assertSame(0, ChatMessage::count());
    }
}
