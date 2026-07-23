<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustSalaryRequest;
use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Mail\WaiterInviteMail;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

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
        $data = $request->validated();
        $message = 'Worker added successfully.';
        $inviteCode = null;

        DB::transaction(function () use ($data, &$message, &$inviteCode): void {
            $worker = Worker::create($data);

            if ($worker->isLoginRole()) {
                [$user, $code] = $this->provisionWaiterAccount($worker);
                $worker->update(['user_id' => $user->id]);
                $inviteCode = $code;
                $message = $this->sendInvite($user, $code);
            }
        });

        return redirect()
            ->route('admin.workers.index')
            ->with('status', $message)
            ->with('invite_code', $inviteCode);
    }

    public function update(UpdateWorkerRequest $request, Worker $worker): RedirectResponse
    {
        $data = $request->validated();
        $wasLoginRole = $worker->isLoginRole();
        $message = 'Worker updated successfully.';
        $inviteCode = null;

        DB::transaction(function () use ($worker, $data, $wasLoginRole, &$message, &$inviteCode): void {
            $hadAccount = (bool) $worker->user_id;

            $worker->update($data);
            $worker->refresh();

            if ($worker->isLoginRole()) {
                if (! $wasLoginRole || ! $hadAccount) {
                    [$user, $code] = $this->provisionWaiterAccount($worker);
                    $worker->update(['user_id' => $user->id]);
                    $inviteCode = $code;
                    $message = $this->sendInvite($user, $code);
                } else {
                    $user = $this->syncWaiterProfile($worker);
                    if ($worker->user_id !== $user->id) {
                        $worker->update(['user_id' => $user->id]);
                    }
                }
            } elseif ($wasLoginRole && $worker->user_id) {
                $linked = User::query()->whereKey($worker->user_id)->where('role', User::ROLE_WAITER)->first();
                $worker->update(['user_id' => null]);
                $linked?->delete();
            }
        });

        return redirect()
            ->route('admin.workers.index')
            ->with('status', $message)
            ->with('invite_code', $inviteCode);
    }

    public function resendInvite(Worker $worker): RedirectResponse
    {
        if (! $worker->isLoginRole() || ! $worker->user_id) {
            return back()->withErrors(['worker' => 'This worker does not have a waiter login.']);
        }

        $user = $worker->user;
        $code = $this->generateChangeCode();

        $user->update([
            'password' => User::DEFAULT_WAITER_PASSWORD,
            'must_change_password' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);
        $user->issuePasswordChangeCode($code);

        $message = $this->sendInvite($user->fresh(), $code);

        return back()
            ->with('status', $message)
            ->with('invite_code', $code);
    }

    public function adjustSalary(AdjustSalaryRequest $request, Worker $worker): RedirectResponse
    {
        $action = $request->validated('action');
        $amount = (int) $request->validated('amount');
        $previous = (int) $worker->salary;

        $newSalary = match ($action) {
            'set' => $amount,
            'increase' => $previous + $amount,
            'decrease' => max(0, $previous - $amount),
        };

        $worker->update(['salary' => $newSalary]);

        $label = match ($action) {
            'set' => 'set to',
            'increase' => 'increased to',
            'decrease' => 'decreased to',
        };

        return back()->with(
            'status',
            "{$worker->name}'s salary {$label} Rs ".number_format($newSalary).' (was Rs '.number_format($previous).').'
        );
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

    /**
     * @return array{0: User, 1: string}
     */
    private function provisionWaiterAccount(Worker $worker): array
    {
        $code = $this->generateChangeCode();

        $user = $worker->user
            ?? User::query()->where('email', $worker->email)->first();

        $attributes = [
            'name' => $worker->name,
            'email' => $worker->email,
            'phone' => $worker->phone,
            'title' => $worker->role,
            'role' => User::ROLE_WAITER,
            'password' => User::DEFAULT_WAITER_PASSWORD,
            'must_change_password' => true,
            'email_verified_at' => now(),
        ];

        if (! $user) {
            $user = User::query()->create($attributes);
        } else {
            $user->fill($attributes);
            $user->save();
        }

        $user->issuePasswordChangeCode($code);

        return [$user->fresh(), $code];
    }

    private function syncWaiterProfile(Worker $worker): User
    {
        $user = $worker->user
            ?? User::query()->where('email', $worker->email)->firstOrFail();

        $user->fill([
            'name' => $worker->name,
            'email' => $worker->email,
            'phone' => $worker->phone,
            'title' => $worker->role,
            'role' => User::ROLE_WAITER,
        ]);
        $user->save();

        return $user->fresh();
    }

    private function generateChangeCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        return collect(range(1, 8))
            ->map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)])
            ->implode('');
    }

    private function sendInvite(User $user, string $code): string
    {
        $defaultPassword = User::DEFAULT_WAITER_PASSWORD;
        $smtpPassword = (string) config('mail.mailers.smtp.password');

        if (config('mail.default') === 'smtp' && $smtpPassword === '') {
            return "Waiter ready. Login name: {$user->name}, password: {$defaultPassword}. Email code (Gmail not configured): {$code}";
        }

        try {
            Mail::to($user->email)->send(new WaiterInviteMail($user, $code, $defaultPassword));

            return "Invite emailed to {$user->email}. Default password: {$defaultPassword}. Change-password code: {$code}";
        } catch (Throwable $exception) {
            report($exception);

            return "Email failed. Login name: {$user->name}, password: {$defaultPassword}, change-password code: {$code}";
        }
    }
}
