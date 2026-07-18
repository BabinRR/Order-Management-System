<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $category = (string) $request->query('category', '');

        $items = MenuItem::query()
            ->where('status', 'Available')
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('customer.menu', [
            'groupedItems' => $items,
            'categories' => ['Starters', 'Mains', 'Desserts', 'Drinks', 'Sides'],
            'activeCategory' => $category,
            'cartCount' => Cart::count(),
            'table' => session('customer_table'),
        ]);
    }
}
