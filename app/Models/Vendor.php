<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;
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

    protected $appends = ['primary_photo'];

    public function getPrimaryPhotoAttribute()
    {
        return $this->profile_photo ?? $this->cover_photo;
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

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }
}
