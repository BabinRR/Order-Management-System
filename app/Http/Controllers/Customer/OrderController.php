<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PlaceOrderRequest;
use App\Models\Order;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        $lines = Cart::detailed();

        if ($lines->isEmpty()) {
            return redirect()
                ->route('customer.cart')
                ->withErrors(['cart' => 'Your cart is empty. Add something from the menu first.']);
        }

        $table = (string) $request->session()->get('customer_table');
        $nextReference = $this->nextReference();

        DB::transaction(function () use ($lines, $table, &$nextReference): void {
            foreach ($lines as $line) {
                Order::create([
                    'reference' => '#'.$nextReference++,
                    'table_number' => $table,
                    'menu_item_id' => $line->item->id,
                    'items_count' => $line->quantity,
                    'total' => $line->line_total,
                    'status' => 'Pending',
                    'service_status' => Order::SERVICE_PENDING,
                    'payment_status' => Order::PAYMENT_UNPAID,
                ]);
            }
        });

        Cart::clear();

        return redirect()
            ->route('customer.bill')
            ->with('status', 'Order placed for table '.$table.'. Track your bill below.');
    }

    private function nextReference(): int
    {
        $latest = Order::query()
            ->orderByDesc('id')
            ->value('reference');

        if (is_string($latest) && preg_match('/(\d+)/', $latest, $matches) === 1) {
            return ((int) $matches[1]) + 1;
        }

        return 5000;
    }
}
