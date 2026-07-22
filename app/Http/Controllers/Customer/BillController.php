<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PayBillRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\KhaltiPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class BillController extends Controller
{
    public function show(): View
    {
        $table = (string) session('customer_table');

        $orders = Order::query()
            ->with('menuItem')
            ->where('table_number', $table)
            ->latest()
            ->get();

        $unpaid = $orders->where('payment_status', Order::PAYMENT_UNPAID);
        $paid = $orders->where('payment_status', Order::PAYMENT_PAID);

        return view('customer-bill', [
            'table' => $table,
            'customerName' => session('customer_name'),
            'orders' => $orders,
            'unpaidOrders' => $unpaid,
            'paidOrders' => $paid,
            'unpaidTotal' => (int) $unpaid->sum('total'),
            'methods' => [
                'cash' => 'Cash',
                'online' => 'Online · Khalti',
            ],
        ]);
    }

    public function pay(PayBillRequest $request, KhaltiPaymentService $khalti): RedirectResponse|Response
    {
        $table = (string) $request->session()->get('customer_table');
        $method = $request->validated('payment_method');

        $unpaid = Order::query()
            ->where('table_number', $table)
            ->where('payment_status', Order::PAYMENT_UNPAID)
            ->get();

        if ($unpaid->isEmpty()) {
            return redirect()
                ->route('customer.bill')
                ->with('status', 'Nothing left to pay on this table.');
        }

        if ($method === 'online') {
            try {
                $payment = $khalti->initiateForOrders(
                    orders: $unpaid,
                    tableNumber: $table,
                    source: Payment::SOURCE_CUSTOMER,
                    returnUrl: route('payments.khalti.callback'),
                    customerName: $request->session()->get('customer_name'),
                );
            } catch (RuntimeException $exception) {
                return redirect()
                    ->route('customer.bill')
                    ->withErrors(['payment' => $exception->getMessage()]);
            }

            return redirect()->away($payment->payment_url);
        }

        DB::transaction(function () use ($unpaid, $method): void {
            foreach ($unpaid as $order) {
                $order->markPaid($method);
            }
        });

        return redirect()
            ->route('customer.bill')
            ->with('status', 'Payment complete. Thank you!');
    }
}
