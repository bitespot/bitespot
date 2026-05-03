<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    protected $casts = [
        'hours' => 'array',
        'isOpen' => 'boolean',
        'isVerified' => 'boolean',
        'isFeatured' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            if (empty($vendor->slug)) {
                $vendor->slug = Str::slug($vendor->business_name) . '-' . uniqid();
            }
            if (!isset($vendor->status)) {
                $vendor->status = 'pending';
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
