<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kitchen.local'],
            [
                'name' => 'Kitchen Admin',
                'phone' => '+977 980-100-0000',
                'title' => 'Owner',
                'role' => User::ROLE_ADMIN,
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'waiter@kitchen.local'],
            [
                'name' => 'Rohan Waiter',
                'phone' => '+977 980-111-2203',
                'title' => 'Waiter',
                'role' => User::ROLE_WAITER,
                'password' => 'password',
            ]
        );

        $workers = [
            ['name' => 'Aarav Sharma', 'role' => 'Head Chef', 'email' => 'aarav@kitchen.local', 'phone' => '+977 980-111-2201', 'shift' => 'Morning', 'status' => 'Active'],
            ['name' => 'Maya Thapa', 'role' => 'Sous Chef', 'email' => 'maya@kitchen.local', 'phone' => '+977 980-111-2202', 'shift' => 'Evening', 'status' => 'Active'],
            ['name' => 'Rohan KC', 'role' => 'Waiter', 'email' => 'rohan@kitchen.local', 'phone' => '+977 980-111-2203', 'shift' => 'Morning', 'status' => 'Active'],
            ['name' => 'Sita Gurung', 'role' => 'Waitress', 'email' => 'sita@kitchen.local', 'phone' => '+977 980-111-2204', 'shift' => 'Evening', 'status' => 'On Leave'],
            ['name' => 'Bikash Rai', 'role' => 'Bartender', 'email' => 'bikash@kitchen.local', 'phone' => '+977 980-111-2205', 'shift' => 'Evening', 'status' => 'Active'],
            ['name' => 'Priya Lama', 'role' => 'Host', 'email' => 'priya@kitchen.local', 'phone' => '+977 980-111-2206', 'shift' => 'Morning', 'status' => 'Active'],
        ];

        foreach ($workers as $worker) {
            Worker::updateOrCreate(['email' => $worker['email']], $worker);
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
            MenuItem::updateOrCreate(['name' => $item['name']], $item);
        }

        if (Order::query()->doesntExist()) {
            $this->seedOrders();
        }

        $this->seedFloorOrders();
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
                $status = fake()->randomElement(['Completed', 'Completed', 'Completed', 'Preparing', 'Served']);
                $service = match ($status) {
                    'Preparing' => Order::SERVICE_PREPARING,
                    'Served', 'Completed' => Order::SERVICE_SERVED,
                    default => Order::SERVICE_PENDING,
                };
                $payment = $status === 'Completed' ? Order::PAYMENT_PAID : Order::PAYMENT_UNPAID;

                Order::create([
                    'reference' => '#'.$reference++,
                    'table_number' => (string) fake()->numberBetween(1, 20),
                    'menu_item_id' => fake()->randomElement($menuItemIds),
                    'worker_id' => fake()->randomElement($workerIds),
                    'items_count' => $itemsCount,
                    'total' => $itemsCount * fake()->numberBetween(300, 900),
                    'status' => $status,
                    'service_status' => $service,
                    'payment_status' => $payment,
                    'payment_method' => $payment === Order::PAYMENT_PAID ? 'cash' : null,
                    'served_at' => $service === Order::SERVICE_SERVED ? $createdAt : null,
                    'paid_at' => $payment === Order::PAYMENT_PAID ? $createdAt : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }

    private function seedFloorOrders(): void
    {
        $menuItemId = MenuItem::query()->value('id');
        $workerId = Worker::query()->value('id');

        if (! $menuItemId) {
            return;
        }

        $samples = [
            ['reference' => '#W1001', 'service_status' => Order::SERVICE_PENDING, 'payment_status' => Order::PAYMENT_UNPAID, 'status' => 'Pending'],
            ['reference' => '#W1002', 'service_status' => Order::SERVICE_PREPARING, 'payment_status' => Order::PAYMENT_UNPAID, 'status' => 'Preparing'],
            ['reference' => '#W1003', 'service_status' => Order::SERVICE_SERVED, 'payment_status' => Order::PAYMENT_UNPAID, 'status' => 'Served'],
            ['reference' => '#W1004', 'service_status' => Order::SERVICE_SERVED, 'payment_status' => Order::PAYMENT_UNPAID, 'status' => 'Served'],
        ];

        foreach ($samples as $index => $sample) {
            Order::updateOrCreate(
                ['reference' => $sample['reference']],
                [
                    'table_number' => (string) ($index + 3),
                    'menu_item_id' => $menuItemId,
                    'worker_id' => $workerId,
                    'items_count' => fake()->numberBetween(2, 5),
                    'total' => fake()->numberBetween(800, 3200),
                    'status' => $sample['status'],
                    'service_status' => $sample['service_status'],
                    'payment_status' => $sample['payment_status'],
                    'payment_method' => null,
                    'served_at' => $sample['service_status'] === Order::SERVICE_SERVED ? now()->subMinutes(20) : null,
                    'paid_at' => null,
                ]
            );
        }
    }
}
