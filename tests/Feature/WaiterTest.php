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
            'payment_method' => 'online',
        ])
        ->assertRedirect(route('waiter.bills.index', ['filter' => 'paid']));

    expect($order->fresh())
        ->payment_status->toBe(Order::PAYMENT_PAID)
        ->payment_method->toBe('online')
        ->status->toBe('Completed');
});

test('waiter sees order items grouped by table number', function () {
    $waiter = User::factory()->waiter()->create();

    Order::factory()->pending()->create([
        'table_number' => '5',
        'items_count' => 2,
    ]);
    Order::factory()->preparing()->create([
        'table_number' => '5',
        'items_count' => 1,
    ]);
    Order::factory()->pending()->create([
        'table_number' => '12',
        'items_count' => 3,
    ]);

    $this->actingAs($waiter)
        ->get(route('waiter.orders.index'))
        ->assertOk()
        ->assertSee('Table 5')
        ->assertSee('Table 12')
        ->assertSee('Orders by Table');

    $this->actingAs($waiter)
        ->get(route('waiter.orders.index', ['table' => '5']))
        ->assertOk()
        ->assertSee('Table 5')
        ->assertSee('Mark all served');
});

test('waiter can mark all open items for a table as served', function () {
    $waiter = User::factory()->waiter()->create();

    $first = Order::factory()->pending()->create(['table_number' => '7']);
    $second = Order::factory()->preparing()->create(['table_number' => '7']);
    $other = Order::factory()->pending()->create(['table_number' => '8']);

    $this->actingAs($waiter)
        ->patch(route('waiter.orders.table.served', '7'))
        ->assertRedirect();

    expect($first->fresh()->service_status)->toBe(Order::SERVICE_SERVED);
    expect($second->fresh()->service_status)->toBe(Order::SERVICE_SERVED);
    expect($other->fresh()->service_status)->toBe(Order::SERVICE_PENDING);
});

test('waiter can collect payment for an entire table', function () {
    $waiter = User::factory()->waiter()->create();

    $first = Order::factory()->served()->create([
        'table_number' => '3',
        'total' => 800,
    ]);
    $second = Order::factory()->served()->create([
        'table_number' => '3',
        'total' => 400,
    ]);

    $this->actingAs($waiter)
        ->get(route('waiter.bills.table', '3'))
        ->assertOk()
        ->assertSee('Table 3')
        ->assertSee('Rs 1,200');

    $this->actingAs($waiter)
        ->post(route('waiter.bills.table.pay', '3'), [
            'payment_method' => 'cash',
        ])
        ->assertRedirect(route('waiter.bills.index', ['filter' => 'paid']));

    expect($first->fresh()->payment_status)->toBe(Order::PAYMENT_PAID);
    expect($second->fresh()->payment_status)->toBe(Order::PAYMENT_PAID);
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
