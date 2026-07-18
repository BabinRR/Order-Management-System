<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Worker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $workers = [
            ['name' => 'Aarav Sharma', 'role' => 'Head Chef', 'email' => 'aarav@kitchen.local', 'phone' => '+977 980-111-2201', 'shift' => 'Morning', 'status' => 'Active'],
            ['name' => 'Maya Thapa', 'role' => 'Sous Chef', 'email' => 'maya@kitchen.local', 'phone' => '+977 980-111-2202', 'shift' => 'Evening', 'status' => 'Active'],
            ['name' => 'Rohan KC', 'role' => 'Waiter', 'email' => 'rohan@kitchen.local', 'phone' => '+977 980-111-2203', 'shift' => 'Morning', 'status' => 'Active'],
            ['name' => 'Sita Gurung', 'role' => 'Waitress', 'email' => 'sita@kitchen.local', 'phone' => '+977 980-111-2204', 'shift' => 'Evening', 'status' => 'On Leave'],
            ['name' => 'Bikash Rai', 'role' => 'Bartender', 'email' => 'bikash@kitchen.local', 'phone' => '+977 980-111-2205', 'shift' => 'Evening', 'status' => 'Active'],
            ['name' => 'Priya Lama', 'role' => 'Host', 'email' => 'priya@kitchen.local', 'phone' => '+977 980-111-2206', 'shift' => 'Morning', 'status' => 'Active'],
        ];

        foreach ($workers as $worker) {
            Worker::create($worker);
        }

        $menuItems = [
            ['name' => 'Grilled Salmon', 'category' => 'Mains', 'price' => 1800, 'description' => 'Char-grilled salmon with lemon butter and seasonal greens.', 'status' => 'Available'],
            ['name' => 'Truffle Pasta', 'category' => 'Mains', 'price' => 1700, 'description' => 'Fresh tagliatelle tossed in black truffle cream sauce.', 'status' => 'Available'],
            ['name' => 'Garden Salad', 'category' => 'Starters', 'price' => 900, 'description' => 'Mixed greens, cherry tomatoes, cucumber, and house vinaigrette.', 'status' => 'Available'],
            ['name' => 'Beef Burger', 'category' => 'Mains', 'price' => 1500, 'description' => 'Angus beef patty, cheddar, pickles, and smoked aioli.', 'status' => 'Available'],
            ['name' => 'Mango Smoothie', 'category' => 'Drinks', 'price' => 600, 'description' => 'Blended ripe mango with yogurt and a hint of mint.', 'status' => 'Available'],
            ['name' => 'Chocolate Lava Cake', 'category' => 'Desserts', 'price' => 850, 'description' => 'Warm molten chocolate cake with vanilla ice cream.', 'status' => 'Sold Out'],
            ['name' => 'Tomato Bruschetta', 'category' => 'Starters', 'price' => 750, 'description' => 'Toasted ciabatta topped with marinated tomatoes and basil.', 'status' => 'Available'],
            ['name' => 'Espresso', 'category' => 'Drinks', 'price' => 350, 'description' => 'Double shot of freshly pulled espresso.', 'status' => 'Available'],
        ];

        foreach ($menuItems as $item) {
            MenuItem::create($item);
        }

        $this->seedOrders();
    }

    private function seedOrders(): void
    {
        $menuItemIds = MenuItem::pluck('id')->all();
        $workerIds = Worker::pluck('id')->all();
        $reference = 4700;

        foreach (range(13, 0) as $daysAgo) {
            $date = now()->subDays($daysAgo);
            $ordersForDay = fake()->numberBetween(35, 95);

            for ($i = 0; $i < $ordersForDay; $i++) {
                $itemsCount = fake()->numberBetween(1, 6);
                $createdAt = $date->copy()->setTime(fake()->numberBetween(10, 22), fake()->numberBetween(0, 59));

                Order::create([
                    'reference' => '#'.$reference++,
                    'table_number' => (string) fake()->numberBetween(1, 20),
                    'menu_item_id' => fake()->randomElement($menuItemIds),
                    'worker_id' => fake()->randomElement($workerIds),
                    'items_count' => $itemsCount,
                    'total' => $itemsCount * fake()->numberBetween(300, 900),
                    'status' => fake()->randomElement(['Completed', 'Completed', 'Completed', 'Preparing', 'Served']),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
