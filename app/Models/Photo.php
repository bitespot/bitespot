<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'url', 'caption', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    protected $appends = ['full_url'];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getFullUrlAttribute(): string
    {
        return Storage::disk('s3')->url($this->attributes['url']);
    }
}
