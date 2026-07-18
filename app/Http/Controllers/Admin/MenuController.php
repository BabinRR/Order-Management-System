<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->query('search', '');
        $category = (string) $request->query('category', '');

        $items = MenuItem::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->latest()
            ->get();

        return view('admin.menu', [
            'items' => $items,
            'search' => $search,
            'activeCategory' => $category,
            'categories' => ['Starters', 'Mains', 'Desserts', 'Drinks', 'Sides'],
            'statuses' => ['Available', 'Sold Out', 'Hidden'],
        ]);
    }

    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        MenuItem::create($request->validated());

        return redirect()->route('admin.menu.index')->with('status', 'Food item added successfully.');
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menu): RedirectResponse
    {
        $menu->update($request->validated());

        return redirect()->route('admin.menu.index')->with('status', 'Food item updated successfully.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()->route('admin.menu.index')->with('status', 'Food item deleted successfully.');
    }
}
