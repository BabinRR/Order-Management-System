<?php

use App\Models\MenuItem;
use App\Models\Order;

beforeEach(function () {
    //
});

test('customer must select a table before browsing the menu', function () {
    $this->get(route('customer.menu'))
        ->assertRedirect(route('customer.home'));
});

test('customer can select a table and browse available menu items', function () {
    MenuItem::factory()->create(['status' => 'Available', 'name' => 'Garden Bowl']);
    MenuItem::factory()->create(['status' => 'Hidden', 'name' => 'Secret Dish']);

    $this->post(route('customer.table.select'), [
        'table_number' => 7,
        'customer_name' => 'Alex',
    ])->assertRedirect(route('customer.menu'));

    $this->withSession([
        'customer_table' => '7',
        'customer_name' => 'Alex',
    ])->get(route('customer.menu'))
        ->assertOk()
        ->assertSee('Garden Bowl')
        ->assertDontSee('Secret Dish');
});

test('customer can add to cart, place order, and pay the bill', function () {
    $item = MenuItem::factory()->create([
        'status' => 'Available',
        'name' => 'Truffle Pasta',
        'price' => 1700,
    ]);

    $this->withSession([
        'customer_table' => '4',
        'customer_name' => 'Sam',
    ])->post(route('customer.cart.add', $item))
        ->assertRedirect();

    $this->withSession([
        'customer_table' => '4',
        'customer_name' => 'Sam',
        'customer_cart' => [
            $item->id => ['menu_item_id' => $item->id, 'quantity' => 2],
        ],
    ])->post(route('customer.order.place'))
        ->assertRedirect(route('customer.bill'));

    $order = Order::query()
        ->where('table_number', '4')
        ->where('menu_item_id', $item->id)
        ->first();

    expect($order)->not->toBeNull()
        ->and($order->items_count)->toBe(2)
        ->and($order->total)->toBe(3400)
        ->and($order->payment_status)->toBe(Order::PAYMENT_UNPAID);

    $this->withSession(['customer_table' => '4'])
        ->post(route('customer.bill.pay'), [
            'payment_method' => 'online',
        ])
        ->assertRedirect(route('customer.bill'));

    expect($order->fresh()->payment_status)->toBe(Order::PAYMENT_PAID)
        ->and($order->fresh()->payment_method)->toBe('online');
});
