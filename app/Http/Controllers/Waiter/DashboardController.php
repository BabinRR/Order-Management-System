<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $activeOrders = Order::query()
            ->with('menuItem')
            ->where(function ($query): void {
                $query->whereIn('service_status', [Order::SERVICE_PENDING, Order::SERVICE_PREPARING])
                    ->orWhere(function ($query): void {
                        $query->where('service_status', Order::SERVICE_SERVED)
                            ->where('payment_status', Order::PAYMENT_UNPAID);
                    });
            })
            ->latest()
            ->get();

        $occupied = $this->groupTables($activeOrders);

        $occupiedNumbers = $occupied
            ->pluck('table_number')
            ->filter(fn ($number) => is_numeric($number))
            ->map(fn ($number) => (int) $number)
            ->all();

        $configured = DiningTable::activeOrdered();

        $available = $configured
            ->reject(fn (DiningTable $table) => in_array($table->number, $occupiedNumbers, true))
            ->map(fn (DiningTable $table) => (object) [
                'table_number' => (string) $table->number,
                'capacity' => $table->seats,
            ])
            ->values();

        return view('waiter-dashboard', [
            'tables' => $occupied,
            'available' => $available,
            'attentionCount' => $occupied->where('needs_attention', true)->count(),
        ]);
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
                $pending = $items->where('service_status', Order::SERVICE_PENDING)->count();
                $preparing = $items->where('service_status', Order::SERVICE_PREPARING)->count();
                $servedUnpaid = $items
                    ->where('service_status', Order::SERVICE_SERVED)
                    ->where('payment_status', Order::PAYMENT_UNPAID)
                    ->count();

                $status = match (true) {
                    $preparing > 0 => 'Preparing',
                    $pending > 0 => 'Waiting to Order',
                    $servedUnpaid > 0 => 'Ready to Serve',
                    default => 'Eating',
                };

                $oldest = $items->sortBy('created_at')->first();
                $seatedMinutes = $oldest?->created_at?->diffInMinutes(now()) ?? 0;
                $capacity = DiningTable::seatsFor($tableNumber);

                return (object) [
                    'table_number' => $tableNumber,
                    'orders' => $items->values(),
                    'item_count' => $items->sum('items_count'),
                    'total' => (int) $items->sum('total'),
                    'status' => $status,
                    'needs_attention' => $pending > 0 || $preparing > 0,
                    'guest_count' => min($capacity, max(1, (int) ceil($items->sum('items_count') / 2))),
                    'capacity' => $capacity,
                    'seated_minutes' => $seatedMinutes,
                    'has_open_service' => $items->whereIn('service_status', [Order::SERVICE_PENDING, Order::SERVICE_PREPARING])->isNotEmpty(),
                    'unpaid_total' => (int) $items->where('payment_status', Order::PAYMENT_UNPAID)->sum('total'),
                ];
            })
            ->sortBy(fn ($table) => is_numeric($table->table_number) ? (int) $table->table_number : PHP_INT_MAX)
            ->values();
    }
}
