<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('customer-cart', [
            'lines' => Cart::detailed(),
            'total' => Cart::total(),
            'table' => session('customer_table'),
        ]);
    }

    public function add(Request $request, MenuItem $menuItem): RedirectResponse
    {
        if ($menuItem->status !== 'Available') {
            return back()->withErrors(['cart' => 'That item is not available right now.']);
        }

        $quantity = max(1, (int) $request->input('quantity', 1));
        Cart::add($menuItem, $quantity);

        return back()->with('status', $menuItem->name.' added to your order.');
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        Cart::update($menuItem->id, (int) $request->input('quantity', 1));

        return redirect()->route('customer.cart')->with('status', 'Cart updated.');
    }

    public function remove(MenuItem $menuItem): RedirectResponse
    {
        Cart::remove($menuItem->id);

        return redirect()->route('customer.cart')->with('status', 'Item removed.');
    }
}
