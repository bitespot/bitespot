<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'title', 'description', 'discount', 'valid_until', 'is_active'];

    protected $casts = [
        'discount'    => 'float',
        'valid_until' => 'datetime',
        'is_active'   => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
