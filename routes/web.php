<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\TimeListController;
use App\Http\Controllers\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('frontend.menu');
    })->name('menu');

    Route::get('/monitoring', [JobOrderController::class, 'monitoring'])->name('monitoring');
    Route::get('/job-order', [JobOrderController::class, 'job-order'])->name('job-order');
    Route::get('/job-order/{date}/{shift}', [JobOrderController::class, 'input'])->name('job-order.input');
    Route::get('/export/{date}/{shift}/{kapal}', [JobOrderController::class, 'export'])->name('export');
    Route::post('/job-orders', [JobOrderController::class, 'store'])->name('joborders.store');
    Route::put('/job-orders/{jobOrder}', [JobOrderController::class, 'update'])->name('joborders.update');
    Route::delete('/job-orders/{jobOrder}', [JobOrderController::class, 'destroy'])->name('joborders.destroy');
    Route::post('/job-orders/{jobOrder}/timelist', [TimeListController::class, 'update'])->name('timelist.update');
    Route::get('/job-order/{date}/{shift}/{kapal?}', [JobOrderController::class, 'input'])->name('job-order.input');
});
