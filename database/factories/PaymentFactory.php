<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->numberBetween(100, 2000);

        return [
            'purchase_order_id' => 'OE-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
            'pidx' => Str::random(22),
            'table_number' => (string) fake()->numberBetween(1, 12),
            'amount' => $amount,
            'amount_paisa' => $amount * 100,
            'status' => Payment::STATUS_PENDING,
            'gateway' => Payment::GATEWAY_KHALTI,
            'source' => Payment::SOURCE_CUSTOMER,
            'order_ids' => [],
            'transaction_id' => null,
            'user_id' => null,
            'payment_url' => 'https://test-pay.khalti.com/?pidx='.Str::random(12),
            'completed_at' => null,
        ];
    }
}
