<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedingEstimate extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'product_id', 'package_size_grams', 'daily_grams', 'runs_out_at', 'reminded_at'];

    protected function casts(): array
    {
        return ['package_size_grams' => 'integer', 'daily_grams' => 'decimal:2', 'runs_out_at' => 'date', 'reminded_at' => 'datetime'];
    }

    /** @return BelongsTo<Pet, $this> */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
