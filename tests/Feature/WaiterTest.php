<?php

use App\Models\Order;
use App\Models\User;

test('waiter is redirected to waiter dashboard after login', function () {
    $waiter = User::factory()->waiter()->create([
        'email' => 'floor@kitchen.local',
        'password' => 'password',
    ]);

    $this->post('/login', [
        'email' => $waiter->email,
        'password' => 'password',
    ])->assertRedirect(route('waiter.dashboard'));
});

test('waiter can view floor dashboard and orders', function () {
    $waiter = User::factory()->waiter()->create();

    $this->actingAs($waiter)
        ->get(route('waiter.dashboard'))
        ->assertOk();

    $this->actingAs($waiter)
        ->get(route('waiter.orders.index'))
        ->assertOk();
});

test('waiter can mark an order as served', function () {
    $waiter = User::factory()->waiter()->create();
    $order = Order::factory()->preparing()->create();

    $this->actingAs($waiter)
        ->patch(route('waiter.orders.served', $order))
        ->assertRedirect();

    expect($order->fresh())
        ->service_status->toBe(Order::SERVICE_SERVED)
        ->served_by->toBe($waiter->id);
});

test('waiter can collect bill payment', function () {
    $waiter = User::factory()->waiter()->create();
    $order = Order::factory()->served()->create([
        'total' => 1500,
    ]);

    $this->actingAs($waiter)
        ->post(route('waiter.bills.pay', $order), [
            'payment_method' => 'card',
        ])
        ->assertRedirect(route('waiter.bills.index', ['filter' => 'paid']));

    expect($order->fresh())
        ->payment_status->toBe(Order::PAYMENT_PAID)
        ->payment_method->toBe('card')
        ->status->toBe('Completed');
});

test('waiter cannot access admin area', function () {
    $waiter = User::factory()->waiter()->create();

    $this->actingAs($waiter)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('admin cannot access waiter area', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('waiter.dashboard'))
        ->assertForbidden();
});
