<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Collection;
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

        $activeByTable = $this->groupTables(
            Order::query()
                ->with('menuItem')
                ->whereIn('service_status', [Order::SERVICE_PENDING, Order::SERVICE_PREPARING])
                ->latest()
                ->get()
        )->take(6);

        $unpaidByTable = $this->groupTables(
            Order::query()
                ->with('menuItem')
                ->where('payment_status', Order::PAYMENT_UNPAID)
                ->where('service_status', Order::SERVICE_SERVED)
                ->latest('served_at')
                ->get()
        )->take(6);

        return view('waiter-dashboard', compact(
            'pending',
            'preparing',
            'servedUnpaid',
            'paidToday',
            'revenueToday',
            'activeByTable',
            'unpaidByTable',
        ));
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return Collection<int, object>
     */
    private function groupTables(Collection $orders): Collection
    {
        return $orders
            ->groupBy(fn (Order $order) => (string) ($order->table_number ?: '—'))
            ->map(function (Collection $items, string $tableNumber) {
                return (object) [
                    'table_number' => $tableNumber,
                    'orders' => $items->values(),
                    'item_count' => $items->sum('items_count'),
                    'total' => $items->sum('total'),
                ];
            })
            ->sortBy(fn ($table) => is_numeric($table->table_number) ? (int) $table->table_number : PHP_INT_MAX)
            ->values();
    }
}
