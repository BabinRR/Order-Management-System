<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DiningTable extends Model
{
    protected $fillable = [
        'number',
        'seats',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'seats' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Ensure default floor tables exist (1–12, 4 seats).
     *
     * @return Collection<int, DiningTable>
     */
    public static function ensureDefaults(int $count = 12, int $seats = 4): Collection
    {
        if (static::query()->exists()) {
            return static::query()->orderBy('number')->get();
        }

        foreach (range(1, $count) as $number) {
            static::query()->create([
                'number' => $number,
                'seats' => $seats,
                'is_active' => true,
            ]);
        }

        return static::query()->orderBy('number')->get();
    }

    /**
     * @return Collection<int, DiningTable>
     */
    public static function activeOrdered(): Collection
    {
        static::ensureDefaults();

        return static::query()
            ->where('is_active', true)
            ->orderBy('number')
            ->get();
    }

    public static function maxNumber(): int
    {
        static::ensureDefaults();

        return (int) static::query()->where('is_active', true)->max('number');
    }

    public static function seatsFor(int|string $tableNumber): int
    {
        static::ensureDefaults();

        $table = static::query()
            ->where('number', (int) $tableNumber)
            ->first();

        return $table?->seats ?? 4;
    }
}
