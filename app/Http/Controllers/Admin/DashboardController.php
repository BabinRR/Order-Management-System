<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Worker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersYesterday = Order::whereDate('created_at', $today->copy()->subDay())->count();
        $revenueToday = (int) Order::whereDate('created_at', $today)->sum('total');
        $revenueYesterday = (int) Order::whereDate('created_at', $today->copy()->subDay())->sum('total');

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

        $floorTables = $this->buildFloor($activeOrders);
        $occupiedCount = $floorTables->where('state', '!=', 'empty')->count();
        $tableTotal = $floorTables->count();

        $avgTicketMinutes = $this->averageTicketMinutes();

        $stats = [
            'orders_today' => $ordersToday,
            'orders_delta' => $this->percentageChange($ordersToday, $ordersYesterday),
            'revenue_today' => $revenueToday,
            'revenue_delta' => $this->percentageChange($revenueToday, $revenueYesterday),
            'active_tables' => $occupiedCount,
            'table_total' => $tableTotal,
            'avg_ticket_minutes' => $avgTicketMinutes,
            'active_workers' => Worker::where('status', 'Active')->count(),
        ];

        $groupedActive = $this->groupActiveOrders($activeOrders);

        $liveFeed = Order::query()
            ->with('menuItem')
            ->latest()
            ->limit(12)
            ->get()
            ->map(function (Order $order): object {
                $message = match (true) {
                    $order->payment_status === Order::PAYMENT_PAID => "Order {$order->reference} was paid",
                    $order->service_status === Order::SERVICE_SERVED => "Order {$order->reference} is now ready",
                    $order->service_status === Order::SERVICE_PREPARING => "Order {$order->reference} is in progress",
                    default => "New order {$order->reference} from table {$order->table_number}",
                };

                return (object) [
                    'message' => $message,
                    'time' => $order->updated_at?->diffForHumans() ?? '',
                ];
            });

        return view('admin-dashboard', [
            'stats' => $stats,
            'activeOrders' => $groupedActive,
            'floorTables' => $floorTables,
            'liveFeed' => $liveFeed,
        ]);
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return Collection<int, object>
     */
    private function groupActiveOrders(Collection $orders): Collection
    {
        return $orders
            ->groupBy(fn (Order $order) => $order->reference ?: (string) $order->id)
            ->map(function (Collection $items, string $reference) {
                $statuses = $items->pluck('service_status');
                $status = match (true) {
                    $statuses->contains(Order::SERVICE_PENDING) => 'pending',
                    $statuses->contains(Order::SERVICE_PREPARING) => 'preparing',
                    default => 'ready',
                };

                $oldest = $items->sortBy('created_at')->first();
                $elapsed = $oldest?->created_at?->diffInSeconds(now()) ?? 0;

                return (object) [
                    'reference' => $reference,
                    'table_number' => $oldest?->table_number ?? '—',
                    'items_count' => (int) $items->sum('items_count'),
                    'total' => (int) $items->sum('total'),
                    'status' => $status,
                    'elapsed' => $this->formatElapsed($elapsed),
                    'order_ids' => $items->pluck('id')->all(),
                    'primary' => $oldest,
                ];
            })
            ->sortByDesc(fn ($row) => $row->primary?->created_at)
            ->values();
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return Collection<int, object>
     */
    private function buildFloor(Collection $orders): Collection
    {
        $byTable = $orders
            ->filter(fn (Order $order) => filled($order->table_number))
            ->groupBy(fn (Order $order) => (string) $order->table_number);

        $maxTable = max(12, $byTable->keys()->filter(fn ($n) => is_numeric($n))->map(fn ($n) => (int) $n)->max() ?: 12);

        return collect(range(1, $maxTable))->map(function (int $number) use ($byTable) {
            $key = (string) $number;
            $items = $byTable->get($key, collect());

            if ($items->isEmpty()) {
                return (object) [
                    'table_number' => $key,
                    'state' => 'empty',
                    'guest_count' => 0,
                    'needs_attention' => false,
                ];
            }

            $pending = $items->where('service_status', Order::SERVICE_PENDING)->count();
            $preparing = $items->where('service_status', Order::SERVICE_PREPARING)->count();
            $needsAttention = $pending > 0 || $preparing > 0;

            return (object) [
                'table_number' => $key,
                'state' => $needsAttention ? 'attention' : 'occupied',
                'guest_count' => min(4, max(1, (int) ceil($items->sum('items_count') / 2))),
                'needs_attention' => $needsAttention,
            ];
        });
    }

    private function averageTicketMinutes(): int
    {
        $served = Order::query()
            ->whereNotNull('served_at')
            ->whereDate('served_at', Carbon::today())
            ->get(['created_at', 'served_at']);

        if ($served->isEmpty()) {
            $active = Order::query()
                ->whereIn('service_status', [Order::SERVICE_PENDING, Order::SERVICE_PREPARING])
                ->get(['created_at']);

            if ($active->isEmpty()) {
                return 0;
            }

            return (int) round($active->avg(fn (Order $order) => $order->created_at->diffInMinutes(now())));
        }

        return (int) round($served->avg(fn (Order $order) => $order->created_at->diffInMinutes($order->served_at)));
    }

    private function formatElapsed(int $seconds): string
    {
        $minutes = intdiv($seconds, 60);
        $secs = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $secs);
    }

    private function percentageChange(int|float $current, int|float $previous): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
