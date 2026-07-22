<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Customer\BillController as CustomerBillController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\TableController;
use App\Http\Controllers\Payment\KhaltiCallbackController;
use App\Http\Controllers\Waiter\BillController;
use App\Http\Controllers\Waiter\DashboardController as WaiterDashboardController;
use App\Http\Controllers\Waiter\OrderController as WaiterOrderController;
use App\Http\Controllers\Waiter\ProfileController as WaiterProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->must_change_password) {
            return redirect('/password/change');
        }

        if ($user->isWaiter()) {
            return redirect('/waiter');
        }

        if (! $user->hasVerifiedEmail()) {
            return redirect('/email/verify');
        }

        return redirect('/admin');
    }

    return redirect('/order');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/password/change', [ForcePasswordChangeController::class, 'edit'])->name('password.force.edit');
    Route::put('/password/change', [ForcePasswordChangeController::class, 'update'])->name('password.force.update');
});

Route::get('/payments/khalti/callback', KhaltiCallbackController::class)
    ->name('payments.khalti.callback');

Route::prefix('order')->name('customer.')->group(function (): void {
    Route::get('/', [TableController::class, 'home'])->name('home');
    Route::post('/table', [TableController::class, 'select'])->name('table.select');
    Route::post('/leave', [TableController::class, 'leave'])->name('leave');

    Route::middleware('customer.table')->group(function (): void {
        Route::get('/menu', [CustomerMenuController::class, 'index'])->name('menu');

        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart/{menuItem}', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{menuItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{menuItem}', [CartController::class, 'remove'])->name('cart.remove');

        Route::post('/place', [CustomerOrderController::class, 'store'])->name('order.place');

        Route::get('/bill', [CustomerBillController::class, 'show'])->name('bill');
        Route::post('/bill/pay', [CustomerBillController::class, 'pay'])->name('bill.pay');
    });
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::put('/floor', [FloorController::class, 'update'])->name('floor.update');
    Route::post('/floor/tables', [FloorController::class, 'store'])->name('floor.tables.store');

    Route::resource('workers', WorkerController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/workers/{worker}/delete', [WorkerController::class, 'destroy'])->name('workers.delete');
    Route::post('/workers/{worker}/resend-invite', [WorkerController::class, 'resendInvite'])->name('workers.resend-invite');
    Route::resource('menu', MenuController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/menu/{menu}/delete', [MenuController::class, 'destroy'])->name('menu.delete');

    Route::patch('/orders/{order}/preparing', [AdminOrderController::class, 'markPreparing'])->name('orders.preparing');
    Route::patch('/orders/{order}/served', [AdminOrderController::class, 'markServed'])->name('orders.served');

    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::prefix('waiter')->name('waiter.')->middleware(['auth', 'role:waiter', 'password.changed'])->group(function (): void {
    Route::get('/', [WaiterDashboardController::class, 'index'])->name('dashboard');

    Route::get('/orders', [WaiterOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/preparing', [WaiterOrderController::class, 'markPreparing'])->name('orders.preparing');
    Route::patch('/orders/{order}/served', [WaiterOrderController::class, 'markServed'])->name('orders.served');
    Route::patch('/orders/table/{table}/served', [WaiterOrderController::class, 'markTableServed'])->name('orders.table.served');

    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::get('/bills/table/{table}', [BillController::class, 'showTable'])->name('bills.table');
    Route::post('/bills/table/{table}/pay', [BillController::class, 'collectTable'])->name('bills.table.pay');
    Route::get('/bills/{order}', [BillController::class, 'show'])->name('bills.show');
    Route::post('/bills/{order}/pay', [BillController::class, 'collect'])->name('bills.pay');

    Route::get('/profile', [WaiterProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [WaiterProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [WaiterProfileController::class, 'updatePassword'])->name('profile.password');
});
