<?php

use App\Models\User;
use App\Models\Worker;

test('the workers page loads', function () {
    Worker::factory()->count(3)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('admin.workers.index'))
        ->assertOk()
        ->assertViewIs('admin.workers');
});

test('a worker can be created', function () {
    $payload = [
        'name' => 'Jane Doe',
        'role' => 'Waiter',
        'email' => 'jane@kitchen.local',
        'phone' => '+977 980-000-0000',
        'shift' => 'Morning',
        'status' => 'Active',
    ];

    $this->actingAs(User::factory()->create())
        ->post(route('admin.workers.store'), $payload)
        ->assertRedirect(route('admin.workers.index'));

    $this->assertDatabaseHas('workers', ['email' => 'jane@kitchen.local']);
});

test('creating a worker requires a name', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('admin.workers.store'), ['email' => 'x@x.com'])
        ->assertSessionHasErrors('name');
});

test('a worker can be updated', function () {
    $worker = Worker::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('admin.workers.update', $worker), [
            'name' => 'Updated Name',
            'role' => $worker->role,
            'email' => $worker->email,
            'phone' => $worker->phone,
            'shift' => 'Evening',
            'status' => 'On Leave',
        ])->assertRedirect(route('admin.workers.index'));

    expect($worker->fresh()->name)->toBe('Updated Name')
        ->and($worker->fresh()->status)->toBe('On Leave');
});

test('a worker can be deleted', function () {
    $worker = Worker::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('admin.workers.destroy', $worker))
        ->assertRedirect(route('admin.workers.index'));

    $this->assertDatabaseMissing('workers', ['id' => $worker->id]);
});
