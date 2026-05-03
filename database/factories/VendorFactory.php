<?php

namespace Database\Factories;

use App\Models\Vendor;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'business_name' => $this->faker->company,
            'slug' => $this->faker->unique()->slug,
            'address' => $this->faker->address,
            'city' => 'Tacloban City',
            'status' => 'approved',
        ];
    }
}
