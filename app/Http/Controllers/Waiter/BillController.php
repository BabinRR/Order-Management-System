<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Waiter\CollectPaymentRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillController extends Controller
{
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', 'unpaid');

        $billsQuery = Order::query()->with(['menuItem', 'server']);

        if ($filter === 'paid') {
            $billsQuery->where('payment_status', Order::PAYMENT_PAID)->latest('paid_at');
        } else {
            $billsQuery->where('payment_status', Order::PAYMENT_UNPAID)
                ->orderByRaw("CASE service_status WHEN 'served' THEN 0 WHEN 'preparing' THEN 1 ELSE 2 END")
                ->latest('updated_at');
        }

        return view('waiter.bills', [
            'bills' => $billsQuery->get(),
            'filter' => $filter,
            'unpaidTotal' => (int) Order::query()->where('payment_status', Order::PAYMENT_UNPAID)->sum('total'),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['menuItem', 'server']);

        return view('waiter.bill-show', [
            'order' => $order,
            'methods' => ['cash', 'card', 'online'],
        ]);
    }

    public function collect(CollectPaymentRequest $request, Order $order): RedirectResponse
    {
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return redirect()
                ->route('waiter.bills.show', $order)
                ->with('status', 'This bill is already paid.');
        }

        $order->markPaid($request->validated('payment_method'), $request->user());

        return redirect()
            ->route('waiter.bills.index', ['filter' => 'paid'])
            ->with('status', "Payment collected for {$order->reference}.");
    }
}
