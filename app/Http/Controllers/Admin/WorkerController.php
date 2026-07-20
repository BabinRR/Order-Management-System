<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkerController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->query('search', '');

        $workers = Worker::query()
            ->with('user')
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
        $data = $request->safe()->except(['password', 'password_confirmation']);
        $password = $request->validated('password');

        $message = 'Worker added successfully.';

        DB::transaction(function () use ($data, $password, &$message): void {
            $worker = Worker::create($data);

            if ($worker->isLoginRole()) {
                $user = $this->syncWaiterUser($worker, $password);
                $worker->update(['user_id' => $user->id]);
                $user->sendEmailVerificationNotification();
                $message = 'Waiter account created — verification email sent.';
            }
        });

        return redirect()->route('admin.workers.index')->with('status', $message);
    }

    public function update(UpdateWorkerRequest $request, Worker $worker): RedirectResponse
    {
        $data = $request->safe()->except(['password', 'password_confirmation']);
        $password = $request->validated('password');
        $wasLoginRole = $worker->isLoginRole();
        $message = 'Worker updated successfully.';

        DB::transaction(function () use ($worker, $data, $password, $wasLoginRole, &$message): void {
            $hadAccount = (bool) $worker->user_id;
            $previousEmail = $worker->user?->email;

            $worker->update($data);
            $worker->refresh();

            if ($worker->isLoginRole()) {
                $user = $this->syncWaiterUser($worker, $password);
                if ($worker->user_id !== $user->id) {
                    $worker->update(['user_id' => $user->id]);
                }
                if (! $wasLoginRole || ! $hadAccount || $previousEmail !== $worker->email) {
                    $user->sendEmailVerificationNotification();
                    $message = 'Waiter account created — verification email sent.';
                }
            } elseif ($wasLoginRole && $worker->user_id) {
                $linked = User::query()->whereKey($worker->user_id)->where('role', User::ROLE_WAITER)->first();
                $worker->update(['user_id' => null]);
                $linked?->delete();
            }
        });

        return redirect()->route('admin.workers.index')->with('status', $message);
    }

    public function destroy(Worker $worker): RedirectResponse
    {
        DB::transaction(function () use ($worker): void {
            $userId = $worker->user_id;
            $worker->delete();

            if ($userId) {
                User::query()
                    ->whereKey($userId)
                    ->where('role', User::ROLE_WAITER)
                    ->delete();
            }
        });

        return redirect()->route('admin.workers.index')->with('status', 'Worker deleted successfully.');
    }

    private function syncWaiterUser(Worker $worker, ?string $password): User
    {
        $user = $worker->user
            ?? User::query()->where('email', $worker->email)->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => $worker->name,
                'email' => $worker->email,
                'phone' => $worker->phone,
                'title' => $worker->role,
                'role' => User::ROLE_WAITER,
                'password' => $password,
                'email_verified_at' => null,
            ]);

            return $user;
        }

        $emailChanged = $user->email !== $worker->email;

        $user->fill([
            'name' => $worker->name,
            'email' => $worker->email,
            'phone' => $worker->phone,
            'title' => $worker->role,
            'role' => User::ROLE_WAITER,
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        if ($password) {
            $user->password = $password;
        }

        $user->save();

        return $user->fresh();
    }
}
