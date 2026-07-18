<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pending = Order::query()->where('service_status', Order::SERVICE_PENDING)->count();
        $preparing = Order::query()->where('service_status', Order::SERVICE_PREPARING)->count();
        $servedUnpaid = Order::query()
            ->where('service_status', Order::SERVICE_SERVED)
            ->where('payment_status', Order::PAYMENT_UNPAID)
            ->count();
        $paidToday = Order::query()
            ->where('payment_status', Order::PAYMENT_PAID)
            ->whereDate('paid_at', today())
            ->count();
        $revenueToday = (int) Order::query()
            ->where('payment_status', Order::PAYMENT_PAID)
            ->whereDate('paid_at', today())
            ->sum('total');

        $recentPending = Order::query()
            ->with('menuItem')
            ->whereIn('service_status', [Order::SERVICE_PENDING, Order::SERVICE_PREPARING])
            ->latest()
            ->limit(8)
            ->get();

        $unpaidBills = Order::query()
            ->with('menuItem')
            ->where('payment_status', Order::PAYMENT_UNPAID)
            ->where('service_status', Order::SERVICE_SERVED)
            ->latest('served_at')
            ->limit(8)
            ->get();

        return view('waiter.dashboard', compact(
            'pending',
            'preparing',
            'servedUnpaid',
            'paidToday',
            'revenueToday',
            'recentPending',
            'unpaidBills',
        ));
    }
}
