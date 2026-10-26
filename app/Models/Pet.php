<?php

namespace App\Models;

use App\Enums\LifeStage;
use App\Enums\PetSex;
use App\Enums\PetSpecies;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    use HasFactory;

    /**
     * user_id is intentionally not fillable: ownership is set through the
     * user's relationship, never from request input.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'species',
        'breed',
        'birthday',
        'weight_kg',
        'sex',
        'allergies',
        'photo_path',
        'public_token',
        'public_name',
        'public_photo',
        'public_vaccinations',
        'public_contact',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'species' => PetSpecies::class,
            'sex' => PetSex::class,
            'birthday' => 'date',
            'weight_kg' => 'decimal:2',
            'allergies' => 'array',
            'public_name' => 'boolean',
            'public_photo' => 'boolean',
            'public_vaccinations' => 'boolean',
            'public_contact' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<PetHealthRecord, $this> */
    public function healthRecords(): HasMany
    {
        return $this->hasMany(PetHealthRecord::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** @return HasMany<FeedingEstimate, $this> */
    public function feedingEstimates(): HasMany
    {
        return $this->hasMany(FeedingEstimate::class);
    }

    public function ageInMonths(): ?int
    {
        return $this->birthday?->diffInMonths(now());
    }

    public function lifeStage(): ?LifeStage
    {
        $months = $this->ageInMonths();
        if ($months === null) {
            return null;
        }

        return match (true) {
            $months < 12 => LifeStage::Young,
            $months >= 84 => LifeStage::Senior,
            default => LifeStage::Adult,
        };
    }

    public function hasCompleteRecommendationProfile(): bool
    {
        return $this->species !== null && $this->birthday !== null && $this->weight_kg !== null;
    }

    public function latestWeightRecords(): HasMany
    {
        return $this->healthRecords()->whereNotNull('weight_kg')->latest('record_date');
    }

    public function healthDueRecords(): HasMany
    {
        return $this->healthRecords()->whereNotNull('next_due_date')->orderBy('next_due_date');
    }
}
