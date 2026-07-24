<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\EsewaPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class EsewaCallbackController extends Controller
{
    public function success(Request $request, EsewaPaymentService $esewa): RedirectResponse
    {
        $encoded = (string) $request->query('data', '');

        if ($encoded === '') {
            return redirect()
                ->route('customer.home')
                ->withErrors(['payment' => 'Missing eSewa payment data.']);
        }

        try {
            $payload = $esewa->decodeCallbackPayload($encoded);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('customer.home')
                ->withErrors(['payment' => $exception->getMessage()]);
        }

        $payment = Payment::query()
            ->where('transaction_uuid', (string) ($payload['transaction_uuid'] ?? ''))
            ->first();

        if (! $payment) {
            return redirect()
                ->route('customer.home')
                ->withErrors(['payment' => 'Payment session not found.']);
        }

        $redirect = $this->redirectFor($payment);

        if (! $esewa->verifyCallbackSignature($payload)) {
            $payment->update(['status' => Payment::STATUS_FAILED]);

            return $redirect->withErrors(['payment' => 'Invalid eSewa payment signature.']);
        }

        try {
            $completed = $esewa->completeIfPaid($payment, $payload);
        } catch (RuntimeException $exception) {
            return $redirect->withErrors(['payment' => $exception->getMessage()]);
        }

        if (! $completed) {
            return $redirect->withErrors(['payment' => 'eSewa payment is not completed yet.']);
        }

        return $redirect->with(
            'status',
            'eSewa payment received · Rs '.number_format($payment->amount).' for table '.$payment->table_number.'.'
        );
    }

    public function failure(Request $request, EsewaPaymentService $esewa): RedirectResponse
    {
        $encoded = (string) $request->query('data', '');
        $payment = null;

        if ($encoded !== '') {
            try {
                $payload = $esewa->decodeCallbackPayload($encoded);
                $payment = Payment::query()
                    ->where('transaction_uuid', (string) ($payload['transaction_uuid'] ?? ''))
                    ->first();
            } catch (RuntimeException) {
                $payment = null;
            }
        }

        if ($payment) {
            $payment->update(['status' => Payment::STATUS_FAILED]);

            return $this->redirectFor($payment)
                ->withErrors(['payment' => 'eSewa payment failed or was canceled.']);
        }

        return redirect()
            ->route('customer.home')
            ->withErrors(['payment' => 'eSewa payment failed or was canceled.']);
    }

    private function redirectFor(Payment $payment): RedirectResponse
    {
        return $payment->source === Payment::SOURCE_WAITER
            ? redirect()->route('waiter.bills.table', $payment->table_number)
            : redirect()->route('customer.bill');
    }
}
