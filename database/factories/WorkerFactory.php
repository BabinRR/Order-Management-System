<?php

namespace Database\Factories;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Worker>
 */
class WorkerFactory extends Factory
{
    protected $model = Worker::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'role' => fake()->randomElement(['Head Chef', 'Sous Chef', 'Waiter', 'Waitress', 'Bartender', 'Host', 'Manager', 'Cashier']),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+977 98#-###-####'),
            'shift' => fake()->randomElement(['Morning', 'Evening', 'Night']),
            'status' => fake()->randomElement(['Active', 'Active', 'Active', 'On Leave', 'Inactive']),
            'salary' => fake()->numberBetween(15000, 45000),
        ];
    }

    public function waiter(): static
    {
        return $this->state(fn () => [
            'role' => 'Waiter',
        ]);
    }
}
