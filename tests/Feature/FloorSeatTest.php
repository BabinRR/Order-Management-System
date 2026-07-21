<?php

use App\Models\DiningTable;
use App\Models\User;

test('admin can update seat counts on the floor', function () {
    DiningTable::ensureDefaults();

    $this->actingAs(User::factory()->create())
        ->put(route('admin.floor.update'), [
            'tables' => [
                ['number' => 1, 'seats' => 6, 'is_active' => 1],
                ['number' => 2, 'seats' => 8, 'is_active' => 1],
                ['number' => 3, 'seats' => 4, 'is_active' => 1],
            ],
        ])
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('status');

    expect(DiningTable::query()->where('number', 1)->value('seats'))->toBe(6)
        ->and(DiningTable::query()->where('number', 2)->value('seats'))->toBe(8)
        ->and(DiningTable::query()->count())->toBe(3);
});

test('admin can add a new table', function () {
    DiningTable::ensureDefaults(2);

    $this->actingAs(User::factory()->create())
        ->post(route('admin.floor.tables.store'))
        ->assertRedirect(route('admin.dashboard'));

    expect(DiningTable::query()->count())->toBe(3)
        ->and(DiningTable::query()->where('number', 3)->value('seats'))->toBe(4);
});

test('customer table picker shows configured seats', function () {
    DiningTable::query()->create(['number' => 1, 'seats' => 6, 'is_active' => true]);
    DiningTable::query()->create(['number' => 2, 'seats' => 2, 'is_active' => true]);

    $this->get(route('customer.home'))
        ->assertOk()
        ->assertSee('Table 1 · 6 seats')
        ->assertSee('Table 2 · 2 seats');
});
