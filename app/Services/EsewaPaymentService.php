<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class EsewaPaymentService
{
    /**
     * @param  Collection<int, Order>  $orders
     * @return array{payment: Payment, form: array<string, string>}
     */
    public function initiateForOrders(
        Collection $orders,
        string $tableNumber,
        string $source,
        ?User $user = null,
    ): array {
        if ($orders->isEmpty()) {
            throw new RuntimeException('No unpaid orders to charge.');
        }

        $amount = (int) $orders->sum('total');

        if ($amount < 1) {
            throw new RuntimeException('Payment amount must be at least Rs 1.');
        }

        $secret = (string) config('esewa.secret_key');
        $productCode = (string) config('esewa.merchant_code');

        if ($secret === '' || $productCode === '') {
            throw new RuntimeException('eSewa is not configured. Set ESEWA_SECRET_KEY and ESEWA_MERCHANT_CODE in .env.');
        }

        $transactionUuid = 'OE-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

        $payment = Payment::query()->create([
            'transaction_uuid' => $transactionUuid,
            'table_number' => $tableNumber,
            'amount' => $amount,
            'status' => Payment::STATUS_PENDING,
            'gateway' => Payment::GATEWAY_ESEWA,
            'source' => $source,
            'order_ids' => $orders->pluck('id')->values()->all(),
            'user_id' => $user?->id,
        ]);

        $amountString = (string) $amount;
        $signedFieldNames = 'total_amount,transaction_uuid,product_code';
        $signature = $this->sign("total_amount={$amountString},transaction_uuid={$transactionUuid},product_code={$productCode}");

        return [
            'payment' => $payment,
            'form' => [
                'amount' => $amountString,
                'tax_amount' => '0',
                'total_amount' => $amountString,
                'transaction_uuid' => $transactionUuid,
                'product_code' => $productCode,
                'product_service_charge' => '0',
                'product_delivery_charge' => '0',
                'success_url' => route('payments.esewa.success'),
                'failure_url' => route('payments.esewa.failure'),
                'signed_field_names' => $signedFieldNames,
                'signature' => $signature,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function decodeCallbackPayload(string $encoded): array
    {
        $json = base64_decode($encoded, true);

        if ($json === false) {
            throw new RuntimeException('Invalid eSewa callback payload.');
        }

        $payload = json_decode($json, true);

        if (! is_array($payload)) {
            throw new RuntimeException('Unable to decode eSewa callback payload.');
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function verifyCallbackSignature(array $payload): bool
    {
        $received = (string) ($payload['signature'] ?? '');
        $signedFieldNames = (string) ($payload['signed_field_names'] ?? '');

        if ($received === '' || $signedFieldNames === '') {
            return false;
        }

        $parts = [];

        foreach (explode(',', $signedFieldNames) as $field) {
            $field = trim($field);

            if ($field === '' || $field === 'signature') {
                continue;
            }

            $parts[] = $field.'='.($payload[$field] ?? '');
        }

        $expected = $this->sign(implode(',', $parts));

        return hash_equals($expected, $received);
    }

    /**
     * @return array<string, mixed>
     */
    public function statusCheck(string $transactionUuid, int|string $totalAmount): array
    {
        $response = Http::acceptJson()->get((string) config('esewa.status_url'), [
            'product_code' => config('esewa.merchant_code'),
            'total_amount' => $totalAmount,
            'transaction_uuid' => $transactionUuid,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Unable to verify eSewa payment status.');
        }

        return $response->json() ?? [];
    }

    public function completeIfPaid(Payment $payment, array $callbackPayload = [], ?User $waiter = null): bool
    {
        if ($payment->isCompleted()) {
            return true;
        }

        $status = $this->statusCheck($payment->transaction_uuid, $payment->amount);
        $remoteStatus = strtoupper((string) ($status['status'] ?? $callbackPayload['status'] ?? ''));

        if ($remoteStatus !== 'COMPLETE') {
            if (in_array($remoteStatus, ['CANCELED', 'NOT_FOUND', 'FULL_REFUND'], true)) {
                $payment->update([
                    'status' => $remoteStatus === 'CANCELED'
                        ? Payment::STATUS_CANCELED
                        : Payment::STATUS_FAILED,
                    'ref_id' => $status['ref_id'] ?? $payment->ref_id,
                ]);
            }

            return false;
        }

        $paidAmount = (int) round((float) ($status['total_amount'] ?? $callbackPayload['total_amount'] ?? 0));

        if ($paidAmount !== $payment->amount) {
            $payment->update(['status' => Payment::STATUS_FAILED]);

            throw new RuntimeException('eSewa amount mismatch for '.$payment->transaction_uuid);
        }

        $orders = Order::query()
            ->whereIn('id', $payment->order_ids ?? [])
            ->where('payment_status', Order::PAYMENT_UNPAID)
            ->get();

        foreach ($orders as $order) {
            $order->markPaid(Payment::GATEWAY_ESEWA, $waiter ?? $payment->user);
        }

        $payment->update([
            'status' => Payment::STATUS_COMPLETED,
            'transaction_code' => $callbackPayload['transaction_code'] ?? $status['ref_id'] ?? null,
            'ref_id' => $status['ref_id'] ?? $callbackPayload['transaction_code'] ?? null,
            'completed_at' => now(),
        ]);

        return true;
    }

    public function formUrl(): string
    {
        return (string) config('esewa.form_url');
    }

    private function sign(string $message): string
    {
        return base64_encode(hash_hmac(
            'sha256',
            $message,
            (string) config('esewa.secret_key'),
            true
        ));
    }
}
