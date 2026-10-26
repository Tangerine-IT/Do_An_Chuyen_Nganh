<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_an_empty_shop_with_friendly_empty_states(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee(__('ui.home.hero.title'))
            ->assertSee(__('ui.home.featured.empty_title'));
    }

    public function test_it_shows_featured_products_with_their_price_brand_and_sale_badge(): void
    {
        $product = Product::factory()->featured()->state(['price' => 300000])->onSale(20)->create(['name' => 'Hạt Chó Nổi Bật']);
        ProductImage::factory()->for($product)->primary()->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Hạt Chó Nổi Bật')
            ->assertSee($product->brand->name)
            ->assertSee(format_vnd(240000))
            ->assertSee('-20%')
            ->assertSee(route('products.show', $product->slug), false);
    }

    public function test_it_hides_inactive_out_of_stock_and_non_featured_products(): void
    {
        Product::factory()->featured()->inactive()->create(['name' => 'San Pham Tat']);
        Product::factory()->featured()->outOfStock()->create(['name' => 'San Pham Het Hang']);
        Product::factory()->create(['name' => 'San Pham Thuong', 'is_featured' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('San Pham Tat')
            ->assertDontSee('San Pham Het Hang')
            ->assertDontSee('San Pham Thuong');
    }

    public function test_it_limits_featured_products_to_eight(): void
    {
        Product::factory()->featured()->count(10)->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertViewHas('featuredProducts', fn ($products) => $products->count() === 8);
    }

    public function test_it_shows_root_categories_services_and_published_posts(): void
    {
        $root = Category::factory()->create(['name' => 'Danh mục Gốc']);
        Category::factory()->create(['name' => 'Danh mục Con', 'parent_id' => $root->id]);
        Category::factory()->inactive()->create(['name' => 'Danh mục Ẩn']);
        Service::factory()->create(['name' => 'Tắm Spa Thử', 'duration_minutes' => 90]);
        Service::factory()->inactive()->create(['name' => 'Dịch Vụ Ẩn']);
        Post::factory()->create(['title' => 'Bài Đã Đăng']);
        Post::factory()->draft()->create(['title' => 'Bài Nháp']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Danh mục Gốc')
            ->assertDontSee('Danh mục Ẩn')
            ->assertSee('Tắm Spa Thử')
            ->assertSee('1 giờ 30 phút')
            ->assertDontSee('Dịch Vụ Ẩn')
            ->assertSee('Bài Đã Đăng')
            ->assertDontSee('Bài Nháp');
    }

    public function test_it_never_leaks_raw_translation_keys(): void
    {
        Product::factory()->featured()->create();
        Service::factory()->create();
        Post::factory()->create();
        Category::factory()->create();

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertDoesNotMatchRegularExpression('/\b(ui|enums)\.[a-z_]+\.[a-z_.]+/', strip_tags($html));
    }

    public function test_guests_see_the_login_links_and_the_pet_profile_call_to_action(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('login'), false)
            ->assertSee(route('register'), false)
            ->assertSee(__('ui.pet_cta.title'));
    }

    public function test_logged_in_users_see_their_name_and_a_logout_form_instead(): void
    {
        $user = User::factory()->create(['name' => 'Nguyễn Minh Anh']);

        $this->actingAs($user)->get(route('home'))
            ->assertOk()
            ->assertSee('Nguyễn Minh Anh')
            ->assertSee(route('logout'), false)
            ->assertDontSee(__('ui.pet_cta.title'));
    }

    public function test_the_header_shows_the_total_quantity_in_the_cart(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create(['quantity' => 2]);
        CartItem::factory()->for($cart)->create(['quantity' => 3]);

        $this->actingAs($user)->get(route('home'))
            ->assertOk()
            ->assertSee(__('ui.cart.count', ['count' => 5]), false);
    }

    public function test_the_header_hides_the_cart_badge_when_the_cart_is_empty(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee(__('ui.cart.count', ['count' => 0]), false);
    }

    public function test_the_category_menu_lists_children_in_the_header(): void
    {
        $root = Category::factory()->create(['name' => 'Thức ăn Test']);
        Category::factory()->create(['name' => 'Hạt Khô Test', 'parent_id' => $root->id]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Hạt Khô Test')
            ->assertSee(route('categories.show', $root->slug), false);
    }

    public function test_navigation_pages_and_placeholders_resolve(): void
    {
        $this->get(route('products.index'))->assertOk()->assertSee(__('ui.catalog.title'));
        $this->get(route('products.show', 'hat-cho-cho'))->assertNotFound();
        $this->get(route('categories.show', 'thuc-an-cho-cho'))->assertNotFound();

        $this->get(route('services.index'))->assertOk()->assertSee(__('ui.coming_soon.title'));
        $this->get(route('blog.index'))->assertOk()->assertSee(__('ui.blog.title'));
        $this->get(route('blog.show', 'bai-viet'))->assertNotFound();

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee(__('ui.cart.title'));
    }
}
