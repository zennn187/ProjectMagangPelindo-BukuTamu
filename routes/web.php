<?php

use App\Http\Controllers\Admin\BlacklistController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\GateReportController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\GateController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KioskController::class, 'index'])->name('kiosk');
Route::view('/welcome', 'welcome')->name('welcome');
Route::view('/landing', 'welcome')->name('landing');
Route::view('/privasi', 'privacy')->name('privacy');
Route::post('/kiosk', [KioskController::class, 'store'])->middleware('throttle:10,1')->name('kiosk.store');
Route::get('/badge/{token}', [KioskController::class, 'badge'])->name('badge');

Route::get('/gate/form', [GateController::class, 'form'])->name('gate.form');
Route::post('/gate/form', [GateController::class, 'store'])->middleware('throttle:10,1')->name('gate.store');
Route::get('/gate/pass/{token}', [GateController::class, 'pass'])->name('gate.pass');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [VisitController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/latest', [VisitController::class, 'latest'])->name('dashboard.latest');
    Route::get('/visits/{visit}/photo', [VisitController::class, 'photo'])->name('visits.photo');

    Route::get('/gate/scanner', [GateController::class, 'scanner'])->name('gate.scanner');
    Route::post('/gate/scanner', [GateController::class, 'scan'])->middleware('throttle:30,1')->name('gate.scan');
    Route::get('/gate/chat/{visit}', [GateController::class, 'chat'])->name('gate.chat');
    Route::get('/gate-reports', [GateReportController::class, 'index'])->name('gate-reports.index');
    Route::get('/gate-reports/latest', [GateReportController::class, 'latest'])->name('gate-reports.latest');

    Route::prefix('visits')->name('visits.')->group(function () {
        Route::post('/{visit}/check-in', [VisitController::class, 'checkIn'])->name('check-in');
        Route::post('/{visit}/check-out', [VisitController::class, 'checkOut'])->name('check-out');
        Route::post('/{visit}/wait', [VisitController::class, 'wait'])->name('wait');
        Route::post('/{visit}/accept-delivery', [VisitController::class, 'acceptDelivery'])->name('accept-delivery');
        Route::post('/{visit}/reject', [VisitController::class, 'reject'])->name('reject');
    });
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('blacklists', BlacklistController::class)->except(['show']);
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
