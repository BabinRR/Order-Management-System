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
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                $request->user()->isWaiter()
                    ? route('waiter.dashboard')
                    : route('admin.dashboard')
            );
        }

        return view('verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->intended(
            $request->user()->isWaiter()
                ? route('waiter.dashboard')
                : route('admin.dashboard')
        )->with('status', 'Email verified successfully.');
    }

    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
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
