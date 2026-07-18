<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $itemsCount = fake()->numberBetween(1, 6);

        return [
            'reference' => '#'.fake()->unique()->numberBetween(1000, 9999),
            'table_number' => (string) fake()->numberBetween(1, 20),
            'menu_item_id' => MenuItem::inRandomOrder()->value('id'),
            'worker_id' => Worker::inRandomOrder()->value('id'),
            'items_count' => $itemsCount,
            'total' => $itemsCount * fake()->numberBetween(300, 900),
            'status' => fake()->randomElement(['Completed', 'Completed', 'Completed', 'Preparing', 'Served']),
        ];
    }
}
