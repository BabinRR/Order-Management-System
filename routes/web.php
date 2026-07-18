<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\WorkerController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('workers', WorkerController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('menu', MenuController::class)->only(['index', 'store', 'update', 'destroy']);
});
