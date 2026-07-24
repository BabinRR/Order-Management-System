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
            'transaction_uuid' => 'OE-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
            'table_number' => (string) fake()->numberBetween(1, 12),
            'amount' => $amount,
            'status' => Payment::STATUS_PENDING,
            'gateway' => Payment::GATEWAY_ESEWA,
            'source' => Payment::SOURCE_CUSTOMER,
            'order_ids' => [],
            'transaction_code' => null,
            'ref_id' => null,
            'user_id' => null,
            'completed_at' => null,
        ];
    }
}
