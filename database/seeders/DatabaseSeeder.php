<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->whereIn('email', [
            'admin@kitchen.local',
            'waiter@kitchen.local',
        ])->delete();

        User::updateOrCreate(
            ['email' => 'davetor321@gmail.com'],
            [
                'name' => 'Admin',
                'phone' => null,
                'title' => 'Owner',
                'role' => User::ROLE_ADMIN,
                'password' => '22246BrB',
                'email_verified_at' => now(),
            ]
        );
    }
}
