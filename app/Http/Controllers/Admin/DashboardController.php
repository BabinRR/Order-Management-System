<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Worker;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $weekStart = Carbon::today()->subDays(6);
        $prevWeekStart = Carbon::today()->subDays(13);

        $revenueToday = (int) Order::whereDate('created_at', $today)->sum('total');
        $revenueYesterday = (int) Order::whereDate('created_at', $today->copy()->subDay())->sum('total');
        $revenueWeek = (int) Order::where('created_at', '>=', $weekStart)->sum('total');
        $revenuePrevWeek = (int) Order::whereBetween('created_at', [$prevWeekStart, $weekStart])->sum('total');

        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersWeek = Order::where('created_at', '>=', $weekStart)->count();
        $avgOrder = $ordersWeek > 0 ? $revenueWeek / $ordersWeek : 0;

        $stats = [
            'revenue_today' => $revenueToday,
            'revenue_today_delta' => $this->percentageChange($revenueToday, $revenueYesterday),
            'revenue_week' => $revenueWeek,
            'revenue_week_delta' => $this->percentageChange($revenueWeek, $revenuePrevWeek),
            'orders_today' => $ordersToday,
            'orders_week' => $ordersWeek,
            'avg_order' => $avgOrder,
            'active_workers' => Worker::where('status', 'Active')->count(),
            'total_workers' => Worker::count(),
            'menu_items' => MenuItem::count(),
            'available_items' => MenuItem::where('status', 'Available')->count(),
        ];

        $weeklyRevenue = collect(range(6, 0))->map(function (int $daysAgo): array {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'day' => $date->format('D'),
                'amount' => (int) Order::whereDate('created_at', $date)->sum('total'),
            ];
        })->all();

        $topItems = Order::query()
            ->selectRaw('menu_item_id, count(*) as orders, sum(total) as revenue')
            ->whereNotNull('menu_item_id')
            ->where('created_at', '>=', $weekStart)
            ->groupBy('menu_item_id')
            ->orderByDesc('revenue')
            ->with('menuItem:id,name')
            ->limit(5)
            ->get()
            ->map(fn (Order $order): array => [
                'name' => $order->menuItem?->name ?? 'Unknown',
                'orders' => (int) $order->orders,
                'revenue' => (int) $order->revenue,
            ])
            ->all();

        $recentOrders = Order::latest()->limit(6)->get();

        return view('admin.dashboard', compact('stats', 'weeklyRevenue', 'topItems', 'recentOrders'));
    }

    private function percentageChange(int|float $current, int|float $previous): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
