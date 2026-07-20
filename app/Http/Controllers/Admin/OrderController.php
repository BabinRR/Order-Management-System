<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function markPreparing(Order $order): RedirectResponse
    {
        Order::query()
            ->where('reference', $order->reference)
            ->where('service_status', Order::SERVICE_PENDING)
            ->get()
            ->each(fn (Order $line) => $line->markPreparing());

        return back()->with('status', "Order {$order->reference} is now in progress.");
    }

    public function markServed(Order $order): RedirectResponse
    {
        Order::query()
            ->where('reference', $order->reference)
            ->whereIn('service_status', [Order::SERVICE_PENDING, Order::SERVICE_PREPARING])
            ->get()
            ->each(fn (Order $line) => $line->markServed(auth()->user()));

        return back()->with('status', "Order {$order->reference} is ready.");
    }
}
