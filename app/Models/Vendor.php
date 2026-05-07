<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'category_id',
        'business_name',
        'slug',
        'description',
        'owner_name',
        'phone',
        'website',
        'email',
        'address',
        'district',
        'city',
        'province',
        'lat',
        'lng',
        'hours',
        'cover_photo',
        'profile_photo',
        'price_tier',
        'tier',
        'status',
        'rejection_reason',
        'is_featured',
        'view_count',
        'avg_rating',
        'review_count',
    ];

    protected function casts(): array
    {
        return [
            'lat'          => 'float',
            'lng'          => 'float',
            'avg_rating'   => 'float',
            'hours'        => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected $appends = ['primary_photo', 'cover_photo_url', 'profile_photo_url', 'price_tier_label'];

    private const PRICE_TIER_MAP = ['$' => '₱', '$$' => '₱₱', '$$$' => '₱₱₱'];

    public function getPriceTierLabelAttribute(): ?string
    {
        return self::PRICE_TIER_MAP[$this->price_tier] ?? $this->price_tier;
    }

    public function getPrimaryPhotoAttribute(): ?string
    {
        $key = $this->profile_photo ?? $this->cover_photo;
        return $key ? Storage::url($key) : null;
    }

    public function getCoverPhotoUrlAttribute(): ?string
    {
        return $this->cover_photo ? Storage::url($this->cover_photo) : null;
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo ? Storage::url($this->profile_photo) : null;
    }

    public function discoveries(): HasMany
    {
        return $this->hasMany(Discovery::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function ownershipApplications(): HasMany
    {
        return $this->hasMany(VendorOwnershipApplication::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }
}
