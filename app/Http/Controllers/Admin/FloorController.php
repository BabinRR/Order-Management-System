<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateFloorRequest;
use App\Models\DiningTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class FloorController extends Controller
{
    public function update(UpdateFloorRequest $request): RedirectResponse
    {
        $rows = $request->validated('tables');

        DB::transaction(function () use ($rows): void {
            $keepNumbers = [];

            foreach ($rows as $row) {
                $number = (int) $row['number'];
                $keepNumbers[] = $number;

                DiningTable::query()->updateOrCreate(
                    ['number' => $number],
                    [
                        'seats' => (int) $row['seats'],
                        'is_active' => (bool) ($row['is_active'] ?? true),
                    ]
                );
            }

            DiningTable::query()
                ->whereNotIn('number', $keepNumbers)
                ->delete();
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Floor seats updated.');
    }

    public function store(): RedirectResponse
    {
        DiningTable::ensureDefaults();

        $next = ((int) DiningTable::query()->max('number')) + 1;

        DiningTable::query()->create([
            'number' => $next,
            'seats' => 4,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('status', "Table {$next} added.");
    }
}
