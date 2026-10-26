<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * user_id is set through the user's relationship. Orders must only be created
     * from server-computed values (never pass raw request data here).
     *
     * @var list<string>
     */
    protected $fillable = [
        'coupon_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'discount_total',
        'shipping_fee',
        'total',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'shipping_ward',
        'shipping_district',
        'shipping_province',
        'note',
        'placed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'payment_method' => PaymentMethod::class,
            'subtotal' => 'integer',
            'discount_total' => 'integer',
            'shipping_fee' => 'integer',
            'total' => 'integer',
            'placed_at' => 'datetime',
            'payment_expires_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Coupon, $this> */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
