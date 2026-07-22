<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class KhaltiPaymentService
{
    /**
     * @param  Collection<int, Order>  $orders
     */
    public function initiateForOrders(
        Collection $orders,
        string $tableNumber,
        string $source,
        string $returnUrl,
        ?User $user = null,
        ?string $customerName = null,
    ): Payment {
        if ($orders->isEmpty()) {
            throw new RuntimeException('No unpaid orders to charge.');
        }

        $amount = (int) $orders->sum('total');
        $amountPaisa = $amount * 100;

        if ($amountPaisa < 1000) {
            throw new RuntimeException('Khalti requires a minimum of Rs 10.');
        }

        $secret = (string) config('khalti.secret_key');

        if ($secret === '') {
            throw new RuntimeException('Khalti secret key is not configured. Set KHALTI_SECRET_KEY in .env.');
        }

        $purchaseOrderId = 'OE-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

        $payment = Payment::query()->create([
            'purchase_order_id' => $purchaseOrderId,
            'table_number' => $tableNumber,
            'amount' => $amount,
            'amount_paisa' => $amountPaisa,
            'status' => Payment::STATUS_PENDING,
            'gateway' => Payment::GATEWAY_KHALTI,
            'source' => $source,
            'order_ids' => $orders->pluck('id')->values()->all(),
            'user_id' => $user?->id,
        ]);

        $payload = [
            'return_url' => $returnUrl,
            'website_url' => rtrim((string) config('app.url'), '/').'/',
            'amount' => $amountPaisa,
            'purchase_order_id' => $purchaseOrderId,
            'purchase_order_name' => 'Table '.$tableNumber.' bill',
            'customer_info' => [
                'name' => $customerName ?: ('Table '.$tableNumber),
                'email' => $user?->email ?: 'guest@ordereasy.local',
                'phone' => '9800000000',
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key '.$secret,
                'Content-Type' => 'application/json',
            ])
                ->acceptJson()
                ->post($this->endpoint('epayment/initiate/'), $payload)
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            $payment->update(['status' => Payment::STATUS_FAILED]);

            throw new RuntimeException(
                'Unable to start Khalti payment: '.$this->errorMessage($exception),
                previous: $exception
            );
        }

        $pidx = (string) ($response['pidx'] ?? '');
        $paymentUrl = (string) ($response['payment_url'] ?? '');

        if ($pidx === '' || $paymentUrl === '') {
            $payment->update(['status' => Payment::STATUS_FAILED]);

            throw new RuntimeException('Khalti did not return a payment URL.');
        }

        $payment->update([
            'pidx' => $pidx,
            'payment_url' => $paymentUrl,
        ]);

        return $payment->fresh();
    }

    /**
     * @return array<string, mixed>
     */
    public function lookup(string $pidx): array
    {
        $secret = (string) config('khalti.secret_key');

        if ($secret === '') {
            throw new RuntimeException('Khalti secret key is not configured.');
        }

        try {
            return Http::withHeaders([
                'Authorization' => 'Key '.$secret,
                'Content-Type' => 'application/json',
            ])
                ->acceptJson()
                ->post($this->endpoint('epayment/lookup/'), ['pidx' => $pidx])
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            throw new RuntimeException(
                'Unable to verify Khalti payment: '.$this->errorMessage($exception),
                previous: $exception
            );
        }
    }

    public function completeIfPaid(Payment $payment, ?User $waiter = null): bool
    {
        if ($payment->isCompleted()) {
            return true;
        }

        if (! $payment->pidx) {
            return false;
        }

        $lookup = $this->lookup($payment->pidx);
        $status = (string) ($lookup['status'] ?? '');

        if ($status !== 'Completed') {
            if (in_array($status, ['User canceled', 'Expired', 'Refunded'], true)) {
                $payment->update([
                    'status' => $status === 'User canceled'
                        ? Payment::STATUS_CANCELED
                        : Payment::STATUS_FAILED,
                    'transaction_id' => $lookup['transaction_id'] ?? $payment->transaction_id,
                ]);
            }

            return false;
        }

        $paidPaisa = (int) ($lookup['total_amount'] ?? 0);

        if ($paidPaisa !== $payment->amount_paisa) {
            $payment->update(['status' => Payment::STATUS_FAILED]);

            throw new RuntimeException('Khalti amount mismatch for '.$payment->purchase_order_id);
        }

        $orders = Order::query()
            ->whereIn('id', $payment->order_ids ?? [])
            ->where('payment_status', Order::PAYMENT_UNPAID)
            ->get();

        foreach ($orders as $order) {
            $order->markPaid(Payment::GATEWAY_KHALTI, $waiter ?? $payment->user);
        }

        $payment->update([
            'status' => Payment::STATUS_COMPLETED,
            'transaction_id' => $lookup['transaction_id'] ?? null,
            'completed_at' => now(),
        ]);

        return true;
    }

    private function endpoint(string $path): string
    {
        return rtrim((string) config('khalti.base_url'), '/').'/'.ltrim($path, '/');
    }

    private function errorMessage(RequestException $exception): string
    {
        $body = $exception->response?->json();

        if (is_array($body)) {
            if (isset($body['detail']) && is_string($body['detail'])) {
                return $body['detail'];
            }

            $first = collect($body)
                ->except(['error_key', 'status_code'])
                ->flatten()
                ->first();

            if (is_string($first)) {
                return $first;
            }
        }

        return $exception->getMessage();
    }
}
