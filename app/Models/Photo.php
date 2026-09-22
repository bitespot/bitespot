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
        $url = $this->attributes['url'] ?? '';
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $defaultDisk = config('filesystems.default', 'public');
        if ($defaultDisk === 's3' && config('filesystems.disks.s3.key')) {
            return Storage::disk('s3')->url($url);
        }

        return Storage::disk('public')->url($url);
    }
}
