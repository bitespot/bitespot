<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discovery extends Model
{
    protected $fillable = ['user_id', 'vendor_id', 'discovered_at'];

    protected function casts(): array
    {
        return [
            'discovered_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
