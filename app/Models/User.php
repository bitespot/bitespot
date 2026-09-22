<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'role', 'avatar', 'location'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
        ];
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) return null;
        if (str_starts_with($this->avatar, 'http')) return $this->avatar;
        
        $defaultDisk = config('filesystems.default', 'public');
        if ($defaultDisk === 's3' && config('filesystems.disks.s3.key')) {
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region', 'ap-southeast-2');
            return "https://{$bucket}.s3.{$region}.amazonaws.com/{$this->avatar}";
        }

        return Storage::disk('public')->url($this->avatar);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function discoveries(): HasMany
    {
        return $this->hasMany(Discovery::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function vendor(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    public function ownershipApplications(): HasMany
    {
        return $this->hasMany(VendorOwnershipApplication::class);
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}