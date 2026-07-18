<?php

use App\Models\User;

test('guests land on the customer ordering home', function () {
    $this->get('/')->assertRedirect(route('customer.home'));
});

test('admin lands on admin dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertRedirect(route('admin.dashboard'));
});

test('waiter lands on waiter dashboard', function () {
    $this->actingAs(User::factory()->waiter()->create())
        ->get('/')
        ->assertRedirect(route('waiter.dashboard'));
});

test('the admin dashboard loads successfully', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk();
});
