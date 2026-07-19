<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Models\Worker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->query('search', '');

        $workers = Worker::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('admin-workers', [
            'workers' => $workers,
            'search' => $search,
            'roles' => ['Head Chef', 'Sous Chef', 'Waiter', 'Waitress', 'Bartender', 'Host', 'Manager', 'Cashier'],
            'shifts' => ['Morning', 'Evening', 'Night'],
            'statuses' => ['Active', 'On Leave', 'Inactive'],
        ]);
    }

    public function store(StoreWorkerRequest $request): RedirectResponse
    {
        Worker::create($request->validated());

        return redirect()->route('admin.workers.index')->with('status', 'Worker added successfully.');
    }

    public function update(UpdateWorkerRequest $request, Worker $worker): RedirectResponse
    {
        $worker->update($request->validated());

        return redirect()->route('admin.workers.index')->with('status', 'Worker updated successfully.');
    }

    public function destroy(Worker $worker): RedirectResponse
    {
        $worker->delete();

        return redirect()->route('admin.workers.index')->with('status', 'Worker deleted successfully.');
    }
}
