<?php

namespace Tests\Feature;

use App\Enums\ProductSpecies;
use App\Livewire\ProductCatalog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_lists_only_active_in_stock_products_by_default(): void
    {
        $visible = Product::factory()->create(['name' => 'Hạt chó hiển thị', 'stock' => 3]);
        Product::factory()->inactive()->create(['name' => 'Sản phẩm ẩn']);
        Product::factory()->outOfStock()->create(['name' => 'Sản phẩm hết hàng']);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee($visible->name)
            ->assertDontSee('Sản phẩm ẩn')
            ->assertDontSee('Sản phẩm hết hàng');
    }

    public function test_catalog_filters_by_search_species_and_sale(): void
    {
        $dog = Product::factory()->forSpecies(ProductSpecies::Dog)->state(['name' => 'Hạt gà cho chó', 'description' => 'Công thức giàu đạm'])->onSale(15)->create();
        Product::factory()->forSpecies(ProductSpecies::Cat)->create(['name' => 'Pate cho mèo']);

        Livewire::test(ProductCatalog::class)
            ->set('search', 'gà')
            ->set('species', ProductSpecies::Dog->value)
            ->set('onSale', true)
            ->assertSee($dog->name)
            ->assertDontSee('Pate cho mèo');
    }

    public function test_catalog_sorting_and_query_state_are_shareable(): void
    {
        $cheap = Product::factory()->create(['name' => 'Sản phẩm giá thấp', 'price' => 100000]);
        $expensive = Product::factory()->create(['name' => 'Sản phẩm giá cao', 'price' => 900000]);

        Livewire::withQueryParams(['q' => 'giá', 'sort' => 'price_desc'])
            ->test(ProductCatalog::class)
            ->assertSet('search', 'giá')
            ->assertSet('sort', 'price_desc')
            ->assertSeeInOrder([$expensive->name, $cheap->name]);
    }

    public function test_inactive_products_and_unknown_slugs_return_not_found(): void
    {
        $inactive = Product::factory()->inactive()->create();
        $this->get(route('products.show', $inactive->slug))->assertNotFound();
        $this->get(route('products.show', 'does-not-exist'))->assertNotFound();
    }

    public function test_product_detail_includes_related_products_and_seo_metadata(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create(['name' => 'Sản phẩm chi tiết', 'meta_title' => 'Tiêu đề SEO']);
        $related = Product::factory()->for($category)->create(['name' => 'Sản phẩm liên quan']);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($related->name)
            ->assertSee('<link rel="canonical" href="'.route('products.show', $product->slug).'"', false)
            ->assertSee('<title>Tiêu đề SEO | '.config('petshop.name').'</title>', false);
    }

    public function test_category_and_brand_pages_scope_the_catalog(): void
    {
        $category = Category::factory()->create(['name' => 'Thức ăn']);
        $brand = Brand::factory()->create(['name' => 'Thương hiệu']);
        $categoryProduct = Product::factory()->for($category)->for($brand)->create(['name' => 'Sản phẩm đúng bộ lọc']);
        Product::factory()->create(['name' => 'Sản phẩm khác']);

        $this->get(route('categories.show', $category->slug))
            ->assertOk()
            ->assertSee($categoryProduct->name);
        $this->get(route('brands.show', $brand->slug))
            ->assertOk()
            ->assertSee($categoryProduct->name);
    }
}
