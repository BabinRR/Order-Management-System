<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'category' => fake()->randomElement(['Starters', 'Mains', 'Desserts', 'Drinks', 'Sides']),
            'price' => fake()->numberBetween(300, 2500),
            'description' => fake()->sentence(10),
            'status' => fake()->randomElement(['Available', 'Available', 'Available', 'Sold Out', 'Hidden']),
        ];
    }
}
