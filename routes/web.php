<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\TimeListController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\MonitoringDataController;
use App\Http\Controllers\Admin\MasterKapalController;
use App\Http\Controllers\Admin\MasterTruckController;
use App\Http\Controllers\Admin\MasterPalkaController;
use App\Http\Controllers\Admin\MasterHoseController;
use App\Http\Controllers\Admin\MasterCabangController;
use App\Http\Controllers\Admin\MasterBoosterController;
use App\Http\Controllers\Admin\AksesMenuController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::middleware(['auth', 'check.user.status'])->group(function () {

    Route::get('/', fn() => view('frontend.menu'))->name('menu');

    Route::middleware(['akses.menu:Monitoring'])->group(function () {

        Route::middleware(['akses.cabang'])->group(function () {
            Route::get('/monitoring', [JobOrderController::class, 'monitoring'])->name('monitoring');
        });

    });

    Route::middleware(['akses.menu:Input Data'])->group(function () {

        Route::middleware(['akses.cabang'])->group(function () {
            Route::get('/job-order/{date}/{shift}/{kapal?}', [JobOrderController::class, 'input'])->name('job-order.input');
            Route::get('/job-order/by-kapal/{kapal}', [JobOrderController::class, 'inputByKapal'])->name('job-order.by-kapal');
        });

    });

    Route::middleware(['akses.cabang'])->group(function () {
        Route::get('/job-orders', [JobOrderController::class, 'index'])->name('joborders.index');
    });
    Route::post('/job-orders', [JobOrderController::class, 'store'])->name('joborders.store');
    Route::put('/job-orders/{jobOrder}', [JobOrderController::class, 'update'])->name('joborders.update');
    Route::delete('/job-orders/{jobOrder}', [JobOrderController::class, 'destroy'])->name('joborders.destroy');
    Route::post('/job-orders/{jobOrder}/timelist', [TimeListController::class, 'update'])->name('timelist.update');
    Route::post('/job-order/{id}/batal', [JobOrderController::class, 'batal'])->name('job-order.batal');

    Route::middleware(['akses.menu:Monitoring Data'])->group(function () {

        Route::middleware(['akses.cabang'])->group(function () {
            Route::get('/export/per-shift/{date}/{shift}/{kapal}', [JobOrderController::class, 'exportByShift'])->name('export.shift');
            Route::get('/export/per-kapal/{date}/{kapal}', [JobOrderController::class, 'exportByKapal'])->name('export.kapal');
            Route::get('/export/all-shift', [JobOrderController::class, 'exportAllShift'])->name('export.all.shift');
            Route::get('/export/all-kapal', [JobOrderController::class, 'exportAllKapal'])->name('export.all.kapal');
        });

    });


    Route::prefix('admin')->group(function () {

        Route::middleware(['akses.menu:Master Data'])->group(function () {

            Route::middleware(['akses.cabang'])->group(function () {
                Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
                Route::get('/chart-data', [DashboardController::class, 'getChartDataAjax'])->name('dashboard.chart-data');
            });

            Route::prefix('masterdata')->group(function () {

                Route::get('/booster', [MasterBoosterController::class, 'index'])->name('master-booster.index');
                Route::post('/booster', [MasterBoosterController::class, 'store'])->name('master-booster.store');
                Route::put('/booster/{booster}', [MasterBoosterController::class, 'update'])->name('master-booster.update');

                Route::get('/kapal', [MasterKapalController::class, 'index'])->name('master-kapal.index');
                Route::post('/kapal', [MasterKapalController::class, 'store'])->name('master-kapal.store');
                Route::put('/kapal/{kapal}', [MasterKapalController::class, 'update'])->name('master-kapal.update');

                Route::get('/truck', [MasterTruckController::class, 'index'])->name('master-truck.index');
                Route::post('/truck', [MasterTruckController::class, 'store'])->name('master-truck.store');
                Route::put('/truck/{truck}', [MasterTruckController::class, 'update'])->name('master-truck.update');
                Route::delete('/truck/{truck}', [MasterTruckController::class, 'destroy'])->name('master-truck.destroy');

                Route::get('/palka', [MasterPalkaController::class, 'index'])->name('master-palka.index');
                Route::post('/palka', [MasterPalkaController::class, 'store'])->name('master-palka.store');
                Route::put('/palka/{palka}', [MasterPalkaController::class, 'update'])->name('master-palka.update');

                Route::get('/hose', [MasterHoseController::class, 'index'])->name('master-hose.index');
                Route::post('/hose', [MasterHoseController::class, 'store'])->name('master-hose.store');
                Route::put('/hose/{hose}', [MasterHoseController::class, 'update'])->name('master-hose.update');

                Route::get('/cabang', [MasterCabangController::class, 'index'])->name('master-cabang.index');
                Route::post('/cabang', [MasterCabangController::class, 'store'])->name('master-cabang.store');
                Route::put('/cabang/{cabang}', [MasterCabangController::class, 'update'])->name('master-cabang.update');
                Route::delete('/cabang/{cabang}', [MasterCabangController::class, 'destroy'])->name('master-cabang.destroy');
            });
        });

        Route::middleware(['akses.menu:Monitoring Data'])->group(function () {

            Route::middleware(['akses.cabang'])->group(function () {
                Route::get('/monitoring-data/export', [MonitoringDataController::class, 'export'])->name('export.all.shift');
                Route::get('/monitoring-data', [MonitoringDataController::class, 'index'])->name('monitoring-data');
                Route::put('/monitoring-data/{id}', [MonitoringDataController::class, 'update'])->name('monitoring-data.update');
            });

        });

        Route::prefix('pengaturan')->middleware(['akses.menu:Pengaturan'])->group(function () {

            Route::middleware(['akses.cabang'])->group(function () {
                Route::get('/akses-menu', [AksesMenuController::class, 'index'])->name('pengaturan-akses.index');
                Route::post('/akses-menu', [AksesMenuController::class, 'store'])->name('pengaturan-akses.store');
                Route::put('/akses-menu/{id}', [AksesMenuController::class, 'update'])->name('pengaturan-akses.update');
                Route::get('/akses-menu/users', [AksesMenuController::class, 'getUsers'])->name('pengaturan-akses.users');

                Route::get('/user', [UserController::class, 'index'])->name('pengaturan-user.index');
                Route::post('/user', [UserController::class, 'store'])->name('pengaturan-user.store');
                Route::put('/user/{user}', [UserController::class, 'update'])->name('pengaturan-user.update');
            });
        });
    });
});
