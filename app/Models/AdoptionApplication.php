<?php

namespace App\Models;

use App\Enums\AdoptionApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdoptionApplication extends Model
{
    use HasFactory;

    protected $fillable = ['adoption_listing_id', 'user_id', 'message', 'phone', 'status', 'admin_note'];

    protected function casts(): array
    {
        return ['status' => AdoptionApplicationStatus::class];
    }

    /** @return BelongsTo<AdoptionListing, $this> */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(AdoptionListing::class, 'adoption_listing_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
