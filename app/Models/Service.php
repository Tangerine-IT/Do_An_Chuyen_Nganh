<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration_minutes',
        'price',
        'image_path',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Human-readable duration, e.g. "45 phút", "1 giờ 30 phút", "1 ngày".
     *
     * @return Attribute<string, never>
     */
    protected function durationLabel(): Attribute
    {
        return Attribute::get(function (): string {
            $minutes = $this->duration_minutes;

            if ($minutes >= 1440 && $minutes % 1440 === 0) {
                return __('ui.common.days', ['count' => intdiv($minutes, 1440)]);
            }

            $hours = intdiv($minutes, 60);
            $rest = $minutes % 60;

            return collect([
                $hours > 0 ? __('ui.common.hours', ['count' => $hours]) : null,
                $rest > 0 ? __('ui.common.minutes', ['count' => $rest]) : null,
            ])->filter()->implode(' ');
        });
    }

    /** @return BelongsToMany<Staff, $this> */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'service_staff');
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
