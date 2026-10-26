<?php

namespace Tests\Feature\Models;

use App\Enums\LifeStage;
use App\Enums\ProductSpecies;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_is_self_referencing(): void
    {
        $parent = Category::factory()->create();
        $second = Category::factory()->create(['parent_id' => $parent->id, 'sort_order' => 2]);
        $first = Category::factory()->create(['parent_id' => $parent->id, 'sort_order' => 1]);

        $this->assertTrue($first->parent->is($parent));
        $this->assertSame([$first->id, $second->id], $parent->children->pluck('id')->all());
        $this->assertSame([$parent->id], Category::roots()->pluck('id')->all());
    }

    public function test_deleting_a_parent_category_promotes_its_children(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $parent->delete();

        $this->assertNull($child->fresh()->parent_id);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Product::factory()->for($category)->create();

        $this->expectException(QueryException::class);

        $category->delete();
    }

    public function test_product_belongs_to_category_and_brand(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $product = Product::factory()->for($category)->for($brand)->create();

        $this->assertTrue($product->category->is($category));
        $this->assertTrue($product->brand->is($brand));
        $this->assertTrue($category->products->first()->is($product));
        $this->assertTrue($brand->products->first()->is($product));
    }

    public function test_deleting_a_brand_keeps_its_products(): void
    {
        $brand = Brand::factory()->create();
        $product = Product::factory()->for($brand)->create();

        $brand->delete();

        $this->assertNull($product->fresh()->brand_id);
    }

    public function test_product_slug_must_be_unique(): void
    {
        Product::factory()->create(['slug' => 'hat-cho-cho']);

        $this->expectException(QueryException::class);

        Product::factory()->create(['slug' => 'hat-cho-cho']);
    }

    public function test_product_images_are_ordered_and_primary_image_is_resolved(): void
    {
        $product = Product::factory()->create();
        ProductImage::factory()->for($product)->create(['sort_order' => 2]);
        $primary = ProductImage::factory()->for($product)->primary()->create(['sort_order' => 1]);

        $this->assertSame(1, $product->images->first()->sort_order);
        $this->assertTrue($product->primaryImage->is($primary));
        $this->assertTrue($primary->product->is($product));
    }

    public function test_deleting_a_product_removes_its_images(): void
    {
        $product = Product::factory()->has(ProductImage::factory()->count(2), 'images')->create();

        $product->delete();

        $this->assertSame(0, ProductImage::count());
    }

    public function test_product_attributes_are_cast(): void
    {
        $product = Product::factory()->create([
            'price' => 289000,
            'sale_price' => null,
            'species' => ProductSpecies::Dog,
            'life_stage' => LifeStage::Young,
            'is_active' => 1,
        ])->fresh();

        $this->assertSame(289000, $product->price);
        $this->assertNull($product->sale_price);
        $this->assertSame(ProductSpecies::Dog, $product->species);
        $this->assertSame(LifeStage::Young, $product->life_stage);
        $this->assertTrue($product->is_active);
    }

    public function test_current_price_uses_the_sale_price_only_when_it_is_lower(): void
    {
        $regular = Product::factory()->create(['price' => 100000, 'sale_price' => null]);
        $onSale = Product::factory()->state(['price' => 100000])->onSale(20)->create();
        $invalidSale = Product::factory()->create(['price' => 100000, 'sale_price' => 100000]);

        $this->assertFalse($regular->is_on_sale);
        $this->assertSame(100000, $regular->current_price);

        $this->assertTrue($onSale->is_on_sale);
        $this->assertSame(80000, $onSale->current_price);

        $this->assertFalse($invalidSale->is_on_sale);
        $this->assertSame(100000, $invalidSale->current_price);
    }

    public function test_active_and_in_stock_scopes(): void
    {
        $visible = Product::factory()->create(['stock' => 5]);
        Product::factory()->inactive()->create();
        Product::factory()->outOfStock()->create();

        $this->assertSame([$visible->id], Product::active()->inStock()->pluck('id')->all());
        $this->assertSame(2, Product::inStock()->count());
    }

    public function test_for_species_scope_includes_products_for_all_pets(): void
    {
        $dog = Product::factory()->forSpecies(ProductSpecies::Dog)->create();
        $all = Product::factory()->forSpecies(ProductSpecies::All)->create();
        Product::factory()->forSpecies(ProductSpecies::Cat)->create();

        $ids = Product::forSpecies(ProductSpecies::Dog)->pluck('id')->all();

        $this->assertEqualsCanonicalizing([$dog->id, $all->id], $ids);
    }
}
