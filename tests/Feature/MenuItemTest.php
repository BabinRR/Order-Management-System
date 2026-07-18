<?php

use App\Models\MenuItem;

test('the menu page loads', function () {
    MenuItem::factory()->count(3)->create();

    $this->get(route('admin.menu.index'))
        ->assertOk()
        ->assertViewIs('admin.menu');
});

test('a menu item can be created', function () {
    $payload = [
        'name' => 'Spring Rolls',
        'category' => 'Starters',
        'price' => 650,
        'description' => 'Crispy vegetable spring rolls.',
        'status' => 'Available',
    ];

    $this->post(route('admin.menu.store'), $payload)
        ->assertRedirect(route('admin.menu.index'));

    $this->assertDatabaseHas('menu_items', ['name' => 'Spring Rolls', 'price' => 650]);
});

test('creating a menu item requires a valid price', function () {
    $this->post(route('admin.menu.store'), [
        'name' => 'Bad Item',
        'category' => 'Mains',
        'price' => -5,
        'status' => 'Available',
    ])->assertSessionHasErrors('price');
});

test('a menu item can be updated', function () {
    $item = MenuItem::factory()->create();

    $this->put(route('admin.menu.update', $item), [
        'name' => 'New Dish',
        'category' => 'Desserts',
        'price' => 999,
        'description' => $item->description,
        'status' => 'Sold Out',
    ])->assertRedirect(route('admin.menu.index'));

    expect($item->fresh()->name)->toBe('New Dish')
        ->and($item->fresh()->price)->toBe(999);
});

test('a menu item can be deleted', function () {
    $item = MenuItem::factory()->create();

    $this->delete(route('admin.menu.destroy', $item))
        ->assertRedirect(route('admin.menu.index'));

    $this->assertDatabaseMissing('menu_items', ['id' => $item->id]);
});
