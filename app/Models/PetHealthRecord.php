<?php

namespace App\Models;

use App\Enums\HealthRecordType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetHealthRecord extends Model
{
    use HasFactory;

    /**
     * pet_id and reminded_at are set by the application, not by request input.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'record_date',
        'next_due_date',
        'weight_kg',
        'note',
        'attachment_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => HealthRecordType::class,
            'record_date' => 'date',
            'next_due_date' => 'date',
            'weight_kg' => 'decimal:2',
            'reminded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Pet, $this> */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function status(): string
    {
        if ($this->next_due_date === null) {
            return 'ok';
        }

        return match (true) {
            $this->next_due_date->isBefore(today()) => 'overdue',
            $this->next_due_date->lte(now()->addDays(14)) => 'due_soon',
            default => 'ok',
        };
    }
}
