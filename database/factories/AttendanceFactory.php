<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'worker_id' => Worker::factory(),
            'date' => now()->toDateString(),
            'status' => fake()->randomElement(Attendance::statuses()),
            'note' => null,
            'marked_by' => User::factory(),
        ];
    }
}
