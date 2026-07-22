<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\KhaltiPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class KhaltiCallbackController extends Controller
{
    public function __invoke(Request $request, KhaltiPaymentService $khalti): RedirectResponse
    {
        $pidx = (string) $request->query('pidx', '');
        $callbackStatus = (string) $request->query('status', '');
        $purchaseOrderId = (string) $request->query('purchase_order_id', '');

        $payment = Payment::query()
            ->when($pidx !== '', fn ($query) => $query->where('pidx', $pidx))
            ->when($pidx === '' && $purchaseOrderId !== '', fn ($query) => $query->where('purchase_order_id', $purchaseOrderId))
            ->first();

        if (! $payment) {
            return redirect()
                ->route('customer.home')
                ->withErrors(['payment' => 'Payment session not found.']);
        }

        $redirect = $payment->source === Payment::SOURCE_WAITER
            ? redirect()->route('waiter.bills.table', $payment->table_number)
            : redirect()->route('customer.bill');

        if (in_array($callbackStatus, ['User canceled', 'Expired'], true)) {
            $payment->update([
                'status' => $callbackStatus === 'User canceled'
                    ? Payment::STATUS_CANCELED
                    : Payment::STATUS_FAILED,
            ]);

            return $redirect->withErrors(['payment' => 'Khalti payment was canceled or expired.']);
        }

        try {
            $completed = $khalti->completeIfPaid($payment);
        } catch (RuntimeException $exception) {
            return $redirect->withErrors(['payment' => $exception->getMessage()]);
        }

        if (! $completed) {
            return $redirect->withErrors(['payment' => 'Khalti payment is not completed yet. Please try again.']);
        }

        return $redirect->with(
            'status',
            'Khalti payment received · Rs '.number_format($payment->amount).' for table '.$payment->table_number.'.'
        );
    }
}
