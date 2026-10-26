<?php

namespace Tests\Feature\Database;

use App\Enums\HealthRecordType;
use App\Enums\PostStatus;
use App\Enums\ProductSpecies;
use App\Enums\UserRole;
use App\Models\AdoptionListing;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Pet;
use App\Models\PetHealthRecord;
use App\Models\Post;
use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The seeders write placeholder SVGs; keep them out of the real storage folder.
        Storage::fake('public');

        $this->seed(DatabaseSeeder::class);
    }

    public function test_it_creates_the_demo_accounts(): void
    {
        $admin = User::where('email', 'admin@petshop.test')->firstOrFail();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertSame(1, User::where('role', UserRole::Admin)->count());
        $this->assertSame(1, User::where('role', UserRole::Staff)->count());

        $customers = User::where('role', UserRole::Customer)->withCount('pets')->get();

        $this->assertCount(5, $customers);
        $customers->each(fn (User $customer) => $this->assertGreaterThan(0, $customer->pets_count));
    }

    public function test_it_creates_pets_with_allergies_for_allergy_matching(): void
    {
        $this->assertGreaterThanOrEqual(8, Pet::count());
        $this->assertTrue(Pet::all()->contains(fn (Pet $pet) => in_array('gà', $pet->allergies, true)));
        $this->assertTrue(Pet::all()->contains(fn (Pet $pet) => in_array('bò', $pet->allergies, true)));
        $this->assertTrue(Pet::all()->contains(fn (Pet $pet) => in_array('cá', $pet->allergies, true)));
        $this->assertTrue(Pet::all()->contains(fn (Pet $pet) => in_array('ngũ cốc', $pet->allergies, true)));
    }

    public function test_it_creates_health_records_including_due_and_overdue_vaccinations(): void
    {
        $vaccinations = PetHealthRecord::where('type', HealthRecordType::Vaccination)->get();

        $this->assertTrue($vaccinations->contains(fn (PetHealthRecord $r) => $r->next_due_date->isBetween(now()->startOfDay(), now()->addDays(7)->endOfDay())));
        $this->assertTrue($vaccinations->contains(fn (PetHealthRecord $r) => $r->next_due_date->isPast()));
        $this->assertSame(0, PetHealthRecord::whereNotNull('reminded_at')->count());
    }

    public function test_it_creates_six_root_categories_with_subcategories(): void
    {
        $roots = Category::roots()->withCount('children')->get();

        $this->assertCount(6, $roots);
        $roots->each(fn (Category $root) => $this->assertGreaterThanOrEqual(2, $root->children_count));
    }

    public function test_it_creates_brands(): void
    {
        $this->assertGreaterThanOrEqual(8, Brand::count());
        $this->assertLessThanOrEqual(10, Brand::count());
    }

    public function test_it_creates_a_realistic_product_catalog(): void
    {
        $this->assertGreaterThanOrEqual(60, Product::count());

        foreach ([ProductSpecies::Dog, ProductSpecies::Cat, ProductSpecies::Bird, ProductSpecies::Fish] as $species) {
            $this->assertGreaterThanOrEqual(5, Product::where('species', $species)->count(), "Not enough {$species->value} products.");
        }

        Product::with('category')->get()->each(function (Product $product) {
            $this->assertNotNull($product->category->parent_id, "{$product->name} must sit in a subcategory.");
            $this->assertNotNull($product->brand_id);
            $this->assertGreaterThan(0, $product->price);

            if ($product->sale_price !== null) {
                $this->assertLessThan($product->price, $product->sale_price, "{$product->name} has an invalid sale price.");
            }
        });

        $this->assertGreaterThan(0, Product::where('stock', 0)->count());
        $this->assertGreaterThan(0, Product::inStock()->count());
        $this->assertGreaterThan(0, Product::where('is_featured', true)->count());
        $this->assertGreaterThan(0, Product::whereNotNull('sale_price')->count());
    }

    public function test_product_ingredients_cover_the_allergens_used_in_tests(): void
    {
        foreach (['gà', 'bò', 'cá', 'ngũ cốc', 'lúa mì', 'ngô'] as $allergen) {
            $this->assertGreaterThan(
                0,
                Product::where('ingredients', 'like', "%{$allergen}%")->count(),
                "No product contains \"{$allergen}\"."
            );
        }

        $this->assertGreaterThan(0, Product::where('ingredients', 'like', '%cừu%')->count());
        $this->assertGreaterThan(0, Product::where('ingredients', 'like', '%vịt%')->count());
    }

    public function test_every_product_has_a_primary_image_stored_on_the_disk(): void
    {
        Product::with('primaryImage')->get()->each(function (Product $product) {
            $this->assertNotNull($product->primaryImage, "{$product->name} has no primary image.");
            Storage::disk('public')->assertExists($product->primaryImage->path);
        });
    }

    public function test_it_creates_services_and_staff(): void
    {
        $this->assertGreaterThanOrEqual(6, Service::count());
        $this->assertLessThanOrEqual(8, Service::count());
        $this->assertSame(3, Staff::count());

        Staff::withCount('services')->get()->each(fn (Staff $staff) => $this->assertGreaterThan(0, $staff->services_count));
        $this->assertSame(1, Staff::whereNotNull('user_id')->count());
    }

    public function test_it_creates_valid_expired_and_exhausted_coupons(): void
    {
        $this->assertGreaterThanOrEqual(3, Coupon::where('is_active', true)->where('expires_at', '>', now())->count());
        $this->assertSame(1, Coupon::where('expires_at', '<', now())->count());
        $this->assertSame(1, Coupon::whereColumn('used_count', '>=', 'usage_limit')->count());
    }

    public function test_it_creates_posts_adoption_listings_and_reviews(): void
    {
        $this->assertSame(5, Post::published()->count());
        $this->assertSame(1, Post::where('status', PostStatus::Draft)->count());
        $this->assertGreaterThanOrEqual(5, AdoptionListing::count());
        $this->assertGreaterThanOrEqual(15, Review::count());
    }

    public function test_loyalty_ledger_matches_the_stored_balances(): void
    {
        User::where('loyalty_points', '>', 0)->each(function (User $user) {
            $this->assertSame(
                $user->loyalty_points,
                $user->loyaltyTransactions()->latest('id')->firstOrFail()->balance_after
            );
        });
    }
}
