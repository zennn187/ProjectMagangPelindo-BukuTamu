<?php

use App\Http\Controllers\Admin\BlacklistController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Guest routes (Kiosk self-service)
|--------------------------------------------------------------------------
*/
Route::get('/', [KioskController::class, 'index'])->name('kiosk');
Route::post('/kiosk', [KioskController::class, 'store'])->name('kiosk.store');
Route::get('/badge/{token}', [KioskController::class, 'badge'])->name('badge');

/*
|--------------------------------------------------------------------------
| Authenticated: dashboard & approvals (receptionist & admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [VisitController::class, 'index'])->name('dashboard');

    // Daftar layanan/pelayanan (dilihat oleh semua operator, dikelola admin)
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

    Route::prefix('visits')->name('visits.')->group(function () {
        Route::post('/{visit}/check-in', [VisitController::class, 'checkIn'])->name('check-in');
        Route::post('/{visit}/check-out', [VisitController::class, 'checkOut'])->name('check-out');
        Route::post('/{visit}/wait', [VisitController::class, 'wait'])->name('wait');
        Route::post('/{visit}/accept-delivery', [VisitController::class, 'acceptDelivery'])->name('accept-delivery');
        Route::post('/{visit}/reject', [VisitController::class, 'reject'])->name('reject');
    });
});

/*
|--------------------------------------------------------------------------
| Admin only (master data, users & reports)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('blacklists', BlacklistController::class)->except(['show']);
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('services', ServiceController::class)->except(['show']);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
