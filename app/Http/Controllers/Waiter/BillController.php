<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Waiter\CollectPaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\EsewaPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class BillController extends Controller
{
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', 'unpaid');
        $table = (string) $request->query('table', '');

        $billsQuery = Order::query()->with(['menuItem', 'server']);

        if ($filter === 'paid') {
            $billsQuery->where('payment_status', Order::PAYMENT_PAID)->latest('paid_at');
        } else {
            $billsQuery->where('payment_status', Order::PAYMENT_UNPAID)
                ->orderByRaw("CASE service_status WHEN 'served' THEN 0 WHEN 'preparing' THEN 1 ELSE 2 END")
                ->latest('updated_at');
        }

        if ($table !== '') {
            $billsQuery->where('table_number', $table);
        }

        $orders = $billsQuery
            ->orderByRaw('CAST(table_number AS INTEGER)')
            ->get();

        $tables = $orders
            ->groupBy(fn (Order $order) => (string) ($order->table_number ?: '—'))
            ->map(function (Collection $items, string $tableNumber) {
                return (object) [
                    'table_number' => $tableNumber,
                    'orders' => $items->values(),
                    'item_count' => $items->sum('items_count'),
                    'total' => $items->sum('total'),
                    'unpaid' => $items->where('payment_status', Order::PAYMENT_UNPAID)->isNotEmpty(),
                ];
            })
            ->sortBy(fn ($tableGroup) => is_numeric($tableGroup->table_number) ? (int) $tableGroup->table_number : PHP_INT_MAX)
            ->values();

        $availableTables = Order::query()
            ->select('table_number')
            ->whereNotNull('table_number')
            ->distinct()
            ->orderByRaw('CAST(table_number AS INTEGER)')
            ->pluck('table_number');

        return view('waiter-bills', [
            'tables' => $tables,
            'filter' => $filter,
            'activeTable' => $table,
            'availableTables' => $availableTables,
            'unpaidTotal' => (int) Order::query()->where('payment_status', Order::PAYMENT_UNPAID)->sum('total'),
        ]);
    }

    public function show(Order $order): View
    {
        return $this->showTable((string) $order->table_number);
    }

    public function showTable(string $table): View
    {
        $orders = Order::query()
            ->with(['menuItem', 'server'])
            ->where('table_number', $table)
            ->latest()
            ->get();

        abort_if($orders->isEmpty(), 404);

        $unpaid = $orders->where('payment_status', Order::PAYMENT_UNPAID);

        return view('waiter-bill-show', [
            'table' => $table,
            'orders' => $orders,
            'unpaidOrders' => $unpaid,
            'unpaidTotal' => (int) $unpaid->sum('total'),
            'methods' => [
                'cash' => 'Cash',
                'online' => 'Online · eSewa',
            ],
        ]);
    }

    public function collect(CollectPaymentRequest $request, Order $order, EsewaPaymentService $esewa): RedirectResponse|View
    {
        return $this->collectTable($request, (string) $order->table_number, $esewa);
    }

    public function collectTable(CollectPaymentRequest $request, string $table, EsewaPaymentService $esewa): RedirectResponse|View
    {
        $unpaid = Order::query()
            ->where('table_number', $table)
            ->where('payment_status', Order::PAYMENT_UNPAID)
            ->get();

        if ($unpaid->isEmpty()) {
            return redirect()
                ->route('waiter.bills.table', $table)
                ->with('status', "Table {$table} has nothing left to pay.");
        }

        $method = $request->validated('payment_method');

        if ($method === 'online') {
            try {
                $initiated = $esewa->initiateForOrders(
                    orders: $unpaid,
                    tableNumber: $table,
                    source: Payment::SOURCE_WAITER,
                    user: $request->user(),
                );
            } catch (RuntimeException $exception) {
                return redirect()
                    ->route('waiter.bills.table', $table)
                    ->withErrors(['payment' => $exception->getMessage()]);
            }

            return view('payments.esewa-redirect', [
                'formUrl' => $esewa->formUrl(),
                'fields' => $initiated['form'],
                'amount' => $initiated['payment']->amount,
            ]);
        }

        DB::transaction(function () use ($unpaid, $method, $request): void {
            foreach ($unpaid as $order) {
                $order->markPaid($method, $request->user());
            }
        });

        return redirect()
            ->route('waiter.bills.index', ['filter' => 'paid'])
            ->with('status', 'Payment collected for table '.$table.' · Rs '.number_format($unpaid->sum('total')).'.');
    }
}
