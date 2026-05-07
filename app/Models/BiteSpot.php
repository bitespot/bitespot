<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BiteSpot extends Model
{
    use HasFactory;

    // These match the inputs from your create.blade.php form
    protected $fillable = [
        'user_id',
        'vendor_id',
        'spot_name',
        'general_photo',
        'spot_rating',
        'spot_review',
        'latitude',
        'longitude'
    ];

    /**
     * The user who posted this BiteSpot.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The users who have liked this BiteSpot.
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bitespot_likes', 'bitespot_id', 'user_id')->withTimestamps();
    }

    /**
     * The users who have saved this BiteSpot.
     */
    public function saves(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bitespot_saves', 'bitespot_id', 'user_id')->withTimestamps();
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}