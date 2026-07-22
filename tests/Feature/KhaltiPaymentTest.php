<?php

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'khalti.secret_key' => 'test_secret_key_demo',
        'khalti.base_url' => 'https://dev.khalti.com/api/v2',
        'app.url' => 'http://127.0.0.1:8000',
    ]);
});

test('selecting online payment redirects customer to khalti sandbox', function () {
    $order = Order::factory()->create([
        'table_number' => '3',
        'total' => 250,
        'payment_status' => Order::PAYMENT_UNPAID,
    ]);

    Http::fake([
        'https://dev.khalti.com/api/v2/epayment/initiate/' => Http::response([
            'pidx' => 'test_pidx_abc123',
            'payment_url' => 'https://test-pay.khalti.com/?pidx=test_pidx_abc123',
            'expires_in' => 1800,
        ], 200),
    ]);

    $this->withSession([
        'customer_table' => '3',
        'customer_name' => 'Alex',
    ])->post(route('customer.bill.pay'), [
        'payment_method' => 'online',
    ])->assertRedirect('https://test-pay.khalti.com/?pidx=test_pidx_abc123');

    $this->assertDatabaseHas('payments', [
        'table_number' => '3',
        'pidx' => 'test_pidx_abc123',
        'amount' => 250,
        'amount_paisa' => 25000,
        'status' => Payment::STATUS_PENDING,
        'source' => Payment::SOURCE_CUSTOMER,
    ]);

    expect($order->fresh()->payment_status)->toBe(Order::PAYMENT_UNPAID);
});

test('khalti callback verifies lookup and marks orders paid', function () {
    $order = Order::factory()->create([
        'table_number' => '5',
        'total' => 400,
        'payment_status' => Order::PAYMENT_UNPAID,
    ]);

    $payment = Payment::factory()->create([
        'purchase_order_id' => 'OE-TEST-001',
        'pidx' => 'lookup_pidx_001',
        'table_number' => '5',
        'amount' => 400,
        'amount_paisa' => 40000,
        'order_ids' => [$order->id],
        'source' => Payment::SOURCE_CUSTOMER,
        'status' => Payment::STATUS_PENDING,
    ]);

    Http::fake([
        'https://dev.khalti.com/api/v2/epayment/lookup/' => Http::response([
            'pidx' => 'lookup_pidx_001',
            'total_amount' => 40000,
            'status' => 'Completed',
            'transaction_id' => 'txn_khalti_001',
            'fee' => 0,
            'refunded' => false,
        ], 200),
    ]);

    $this->withSession(['customer_table' => '5'])
        ->get(route('payments.khalti.callback', [
            'pidx' => 'lookup_pidx_001',
            'status' => 'Completed',
            'purchase_order_id' => 'OE-TEST-001',
            'amount' => 40000,
        ]))
        ->assertRedirect(route('customer.bill'))
        ->assertSessionHas('status');

    expect($order->fresh())
        ->payment_status->toBe(Order::PAYMENT_PAID)
        ->payment_method->toBe('khalti');

    expect($payment->fresh())
        ->status->toBe(Payment::STATUS_COMPLETED)
        ->transaction_id->toBe('txn_khalti_001');
});

test('cash payment still settles immediately without khalti', function () {
    $order = Order::factory()->create([
        'table_number' => '2',
        'total' => 150,
        'payment_status' => Order::PAYMENT_UNPAID,
    ]);

    Http::fake();

    $this->withSession(['customer_table' => '2'])
        ->post(route('customer.bill.pay'), ['payment_method' => 'cash'])
        ->assertRedirect(route('customer.bill'));

    expect($order->fresh())
        ->payment_status->toBe(Order::PAYMENT_PAID)
        ->payment_method->toBe('cash');

    Http::assertNothingSent();
});
