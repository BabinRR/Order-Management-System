<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SelectTableRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    public function home(): View
    {
        return view('customer.home', [
            'table' => session('customer_table'),
            'customerName' => session('customer_name'),
        ]);
    }

    public function select(SelectTableRequest $request): RedirectResponse
    {
        $request->session()->put('customer_table', (string) $request->validated('table_number'));
        $request->session()->put('customer_name', $request->validated('customer_name'));

        return redirect()
            ->route('customer.menu')
            ->with('status', 'Table '.$request->validated('table_number').' is ready. Browse the menu and order.');
    }

    public function leave(Request $request): RedirectResponse
    {
        $request->session()->forget(['customer_table', 'customer_name', 'customer_cart']);

        return redirect()
            ->route('customer.home')
            ->with('status', 'Session ended. You can start again anytime.');
    }
}
