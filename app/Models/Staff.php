<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A service provider (groomer, vet). Not the same as a User with role = staff;
 * the optional user_id links a provider to a login account.
 */
class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'name',
        'title',
        'phone',
        'bio',
        'photo_path',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsToMany<Service, $this> */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_staff');
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** @return HasMany<StaffSchedule, $this> */
    public function schedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class);
    }

    /**
     * @param  Builder<Staff>  $query
     * @return Builder<Staff>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
