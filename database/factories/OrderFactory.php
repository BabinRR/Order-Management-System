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
            'menu_item_id' => MenuItem::factory(),
            'worker_id' => Worker::factory(),
            'items_count' => $itemsCount,
            'total' => $itemsCount * fake()->numberBetween(300, 900),
            'status' => 'Pending',
            'service_status' => Order::SERVICE_PENDING,
            'payment_status' => Order::PAYMENT_UNPAID,
            'payment_method' => null,
            'served_at' => null,
            'paid_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'Pending',
            'service_status' => Order::SERVICE_PENDING,
            'payment_status' => Order::PAYMENT_UNPAID,
        ]);
    }

    public function preparing(): static
    {
        return $this->state(fn () => [
            'status' => 'Preparing',
            'service_status' => Order::SERVICE_PREPARING,
        ]);
    }

    public function served(): static
    {
        return $this->state(fn () => [
            'status' => 'Served',
            'service_status' => Order::SERVICE_SERVED,
            'served_at' => now(),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'Completed',
            'service_status' => Order::SERVICE_SERVED,
            'payment_status' => Order::PAYMENT_PAID,
            'payment_method' => 'cash',
            'served_at' => now()->subMinutes(10),
            'paid_at' => now(),
        ]);
    }
}
