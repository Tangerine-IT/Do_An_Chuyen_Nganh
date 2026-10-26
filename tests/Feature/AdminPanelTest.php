<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_access_admin_crud(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_staff_can_manage_orders_but_not_products_or_users(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('admin.orders.index'))->assertOk();
        $this->actingAs($staff)->get(route('admin.products.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_admin_can_create_product_with_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Sản phẩm quản trị',
            'slug' => 'san-pham-quan-tri',
            'price' => 100000,
            'stock' => 8,
            'species' => 'dog',
            'is_active' => '1',
            'is_featured' => '0',
            'images' => [UploadedFile::fake()->create('food.jpg', 100, 'image/jpeg')],
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::query()->where('slug', 'san-pham-quan-tri')->firstOrFail();
        $this->assertDatabaseHas('product_images', ['product_id' => $product->id, 'is_primary' => true]);
    }

    public function test_admin_rejects_invalid_order_status_transition(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), ['status' => 'completed'])
            ->assertServerError();

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_admin_can_advance_order_status_and_staff_cannot_change_users(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertSame(OrderStatus::Confirmed, $order->fresh()->status);
    }
}
