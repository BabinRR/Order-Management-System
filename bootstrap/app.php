<?php

use App\Http\Middleware\EnsureCustomerHasTable;
use App\Http\Middleware\EnsurePasswordIsChanged;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'customer.table' => EnsureCustomerHasTable::class,
            'password.changed' => EnsurePasswordIsChanged::class,
        ]);

        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo(function (): string {
            $user = auth()->user();

            if ($user?->must_change_password) {
                return '/password/change';
            }

            if ($user?->isWaiter()) {
                return '/waiter';
            }

            if ($user && ! $user->hasVerifiedEmail()) {
                return '/email/verify';
            }

            return '/admin';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
