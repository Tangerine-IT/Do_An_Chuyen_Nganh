<?php

namespace App\Models;

use App\Enums\LifeStage;
use App\Enums\ProductSpecies;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'ingredients',
        'price',
        'sale_price',
        'stock',
        'package_size_grams',
        'daily_grams_per_kg',
        'species',
        'life_stage',
        'min_pet_weight_kg',
        'max_pet_weight_kg',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'sale_price' => 'integer',
            'stock' => 'integer',
            'package_size_grams' => 'integer',
            'daily_grams_per_kg' => 'decimal:2',
            'species' => ProductSpecies::class,
            'life_stage' => LifeStage::class,
            'min_pet_weight_kg' => 'decimal:2',
            'max_pet_weight_kg' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return BelongsTo<Brand, $this> */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** @return HasMany<ProductImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return HasOne<ProductImage, $this> */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Whether a valid sale price is currently applied.
     *
     * @return Attribute<bool, never>
     */
    protected function isOnSale(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->sale_price !== null && $this->sale_price < $this->price
        );
    }

    /**
     * The price a customer pays right now (sale price when on sale).
     *
     * @return Attribute<int, never>
     */
    protected function currentPrice(): Attribute
    {
        return Attribute::get(
            fn (): int => $this->is_on_sale ? $this->sale_price : $this->price
        );
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Adds rating_avg and rating_count computed from approved reviews only.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeWithRatingSummary(Builder $query): Builder
    {
        $approved = fn (Builder $reviews) => $reviews->where('is_approved', true);

        return $query
            ->withAvg(['reviews as rating_avg' => $approved], 'rating')
            ->withCount(['reviews as rating_count' => $approved]);
    }

    /**
     * Products made for the given species, including those suitable for all pets.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeForSpecies(Builder $query, ProductSpecies $species): Builder
    {
        return $query->whereIn('species', [$species->value, ProductSpecies::All->value]);
    }
}
