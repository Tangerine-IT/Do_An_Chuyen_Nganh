<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * role and loyalty_points are intentionally excluded so they can never be
     * changed through mass assignment of request data (privilege escalation).
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
    ];

    /**
     * Mirror the column defaults so freshly created models are consistent
     * with what is stored, without needing a refresh().
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'customer',
        'loyalty_points' => 0,
        'is_active' => true,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'loyalty_points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<Pet, $this> */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    /** @return HasMany<Address, $this> */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return HasOne<Cart, $this> */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** @return BelongsToMany<Product, $this> */
    public function wishlistProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** @return HasMany<Wishlist, $this> */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /** @return HasOne<Staff, $this> */
    public function staffProfile(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    /** @return HasMany<Post, $this> */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /** @return HasMany<AdoptionListing, $this> */
    public function adoptionListings(): HasMany
    {
        return $this->hasMany(AdoptionListing::class);
    }

    /** @return HasMany<AdoptionApplication, $this> */
    public function adoptionApplications(): HasMany
    {
        return $this->hasMany(AdoptionApplication::class);
    }

    /**
     * Loyalty points ledger (the current balance is the loyalty_points column).
     *
     * @return HasMany<LoyaltyPoint, $this>
     */
    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /** @return HasMany<ChatMessage, $this> */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin);
    }

    public function isStaff(): bool
    {
        return $this->hasRole(UserRole::Staff);
    }

    public function isCustomer(): bool
    {
        return $this->hasRole(UserRole::Customer);
    }
}
