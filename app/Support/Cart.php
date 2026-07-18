<?php

namespace App\Support;

use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class Cart
{
    private const SESSION_KEY = 'customer_cart';

    /**
     * @return array<int, array{menu_item_id: int, quantity: int}>
     */
    public static function items(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public static function count(): int
    {
        return collect(self::items())->sum('quantity');
    }

    public static function add(MenuItem $item, int $quantity = 1): void
    {
        $quantity = max(1, $quantity);
        $items = self::items();
        $id = $item->id;

        if (isset($items[$id])) {
            $items[$id]['quantity'] += $quantity;
        } else {
            $items[$id] = [
                'menu_item_id' => $id,
                'quantity' => $quantity,
            ];
        }

        Session::put(self::SESSION_KEY, $items);
    }

    public static function update(int $menuItemId, int $quantity): void
    {
        $items = self::items();

        if (! isset($items[$menuItemId])) {
            return;
        }

        if ($quantity <= 0) {
            unset($items[$menuItemId]);
        } else {
            $items[$menuItemId]['quantity'] = $quantity;
        }

        Session::put(self::SESSION_KEY, $items);
    }

    public static function remove(int $menuItemId): void
    {
        $items = self::items();
        unset($items[$menuItemId]);
        Session::put(self::SESSION_KEY, $items);
    }

    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * @return Collection<int, object{item: MenuItem, quantity: int, line_total: int}>
     */
    public static function detailed(): Collection
    {
        $cart = self::items();

        if ($cart === []) {
            return collect();
        }

        $menuItems = MenuItem::query()
            ->whereIn('id', array_keys($cart))
            ->where('status', 'Available')
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->filter(fn (array $row) => $menuItems->has($row['menu_item_id']))
            ->map(function (array $row) use ($menuItems) {
                $item = $menuItems->get($row['menu_item_id']);

                return (object) [
                    'item' => $item,
                    'quantity' => $row['quantity'],
                    'line_total' => $item->price * $row['quantity'],
                ];
            })
            ->values();
    }

    public static function total(): int
    {
        return (int) self::detailed()->sum('line_total');
    }
}
