<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', 'active');

        $orders = Order::query()
            ->with(['menuItem', 'server'])
            ->when($filter === 'pending', fn ($query) => $query->where('service_status', Order::SERVICE_PENDING))
            ->when($filter === 'preparing', fn ($query) => $query->where('service_status', Order::SERVICE_PREPARING))
            ->when($filter === 'served', fn ($query) => $query->where('service_status', Order::SERVICE_SERVED))
            ->when($filter === 'active', function ($query): void {
                $query->where(function ($query): void {
                    $query->whereIn('service_status', [Order::SERVICE_PENDING, Order::SERVICE_PREPARING])
                        ->orWhere(function ($query): void {
                            $query->where('service_status', Order::SERVICE_SERVED)
                                ->where('payment_status', Order::PAYMENT_UNPAID);
                        });
                });
            })
            ->latest()
            ->get();

        return view('waiter.orders', [
            'orders' => $orders,
            'filter' => $filter,
        ]);
    }

    public function markPreparing(Order $order): RedirectResponse
    {
        $order->markPreparing();

        return back()->with('status', "Order {$order->reference} is now preparing.");
    }

    public function markServed(Order $order): RedirectResponse
    {
        $order->markServed(auth()->user());

        return back()->with('status', "Order {$order->reference} marked as served.");
    }
}
