<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->must_change_password) {
            return redirect()->route('password.force.edit');
        }

        if ($user->isWaiter()) {
            if (! $user->hasVerifiedEmail()) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            return redirect()->route('waiter.dashboard');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return view('verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        if ($request->user()->must_change_password) {
            return redirect()->route('password.force.edit')
                ->with('status', 'Email verified. Please set your password.');
        }

        return redirect()->intended(
            $request->user()->isWaiter()
                ? route('waiter.dashboard')
                : route('admin.dashboard')
        )->with('status', 'Email verified successfully.');
    }

    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->must_change_password) {
            return redirect()->route('password.force.edit');
        }

        if ($request->user()->isWaiter() || $request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                $request->user()->isWaiter()
                    ? route('waiter.dashboard')
                    : route('admin.dashboard')
            );
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent to your email.');
    }
}
