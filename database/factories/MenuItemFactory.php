<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 50, 1000),
            'photo' => null,
            'category' => $this->faker->randomElement(['Mains', 'Drinks', 'Desserts', 'Appetizers']),
            'is_available' => true,
            'sort_order' => 0,
        ];
    }
}
