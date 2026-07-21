<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForcePasswordChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ForcePasswordChangeController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user?->must_change_password) {
            return redirect()->to(
                $user?->isWaiter() ? route('waiter.dashboard') : route('admin.dashboard')
            );
        }

        return view('force-password-change', [
            'user' => $user,
        ]);
    }

    public function update(ForcePasswordChangeRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'password' => $request->validated('password'),
            'must_change_password' => false,
        ]);

        $user->clearPasswordChangeCode();

        $home = $user->isWaiter()
            ? route('waiter.dashboard')
            : route('admin.dashboard');

        return redirect()->to($home)->with('status', 'Password updated. Welcome aboard!');
    }
}
