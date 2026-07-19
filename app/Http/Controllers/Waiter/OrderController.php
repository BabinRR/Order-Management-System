<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', 'active');
        $table = (string) $request->query('table', '');

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
            ->when($table !== '', fn ($query) => $query->where('table_number', $table))
            ->orderByRaw('CAST(table_number AS INTEGER)')
            ->latest()
            ->get();

        $tables = $this->groupByTable($orders);

        $availableTables = Order::query()
            ->select('table_number')
            ->whereNotNull('table_number')
            ->distinct()
            ->orderByRaw('CAST(table_number AS INTEGER)')
            ->pluck('table_number');

        return view('waiter.orders', [
            'tables' => $tables,
            'filter' => $filter,
            'activeTable' => $table,
            'availableTables' => $availableTables,
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

    public function markTableServed(Request $request, string $table): RedirectResponse
    {
        $orders = Order::query()
            ->where('table_number', $table)
            ->whereIn('service_status', [Order::SERVICE_PENDING, Order::SERVICE_PREPARING])
            ->get();

        foreach ($orders as $order) {
            $order->markServed($request->user());
        }

        return back()->with('status', "All open items for table {$table} marked as served.");
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return Collection<string, object>
     */
    private function groupByTable(Collection $orders): Collection
    {
        return $orders
            ->groupBy(fn (Order $order) => (string) ($order->table_number ?: '—'))
            ->map(function (Collection $items, string $tableNumber) {
                return (object) [
                    'table_number' => $tableNumber,
                    'orders' => $items->values(),
                    'item_count' => $items->sum('items_count'),
                    'total' => $items->sum('total'),
                    'pending_count' => $items->where('service_status', Order::SERVICE_PENDING)->count(),
                    'preparing_count' => $items->where('service_status', Order::SERVICE_PREPARING)->count(),
                    'served_count' => $items->where('service_status', Order::SERVICE_SERVED)->count(),
                    'unpaid_total' => $items->where('payment_status', Order::PAYMENT_UNPAID)->sum('total'),
                    'has_open_service' => $items->whereIn('service_status', [Order::SERVICE_PENDING, Order::SERVICE_PREPARING])->isNotEmpty(),
                ];
            })
            ->sortBy(fn ($table) => is_numeric($table->table_number) ? (int) $table->table_number : PHP_INT_MAX)
            ->values();
    }
}
