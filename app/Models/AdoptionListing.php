<?php

namespace App\Models;

use App\Enums\AdoptionStatus;
use App\Enums\PetSex;
use App\Enums\PetSpecies;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdoptionListing extends Model
{
    use HasFactory;

    /**
     * user_id is set through the user's relationship; status is changed by moderation.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'species',
        'breed',
        'sex',
        'age_months',
        'description',
        'is_vaccinated',
        'is_neutered',
        'photo_path',
        'gallery_paths',
        'health_notes',
        'vaccination_notes',
        'contact_name',
        'contact_phone',
        'city',
        'latitude',
        'longitude',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'species' => PetSpecies::class,
            'sex' => PetSex::class,
            'status' => AdoptionStatus::class,
            'age_months' => 'integer',
            'is_vaccinated' => 'boolean',
            'is_neutered' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'gallery_paths' => 'array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<AdoptionApplication, $this> */
    public function applications(): HasMany
    {
        return $this->hasMany(AdoptionApplication::class);
    }

    /**
     * @param  Builder<AdoptionListing>  $query
     * @return Builder<AdoptionListing>
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', AdoptionStatus::Available);
    }
}
