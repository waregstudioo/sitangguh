<?php

use App\Http\Controllers\AlertStatusController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\LogisticController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Village Disaster Management & Citizen Reporting Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [ReportController::class, 'index'])->name('home');
Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Admin Posko)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Unified Admin Dashboard: Protected by auth middleware
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth')->group(function (): void {
    Route::get('/', [AlertStatusController::class, 'index'])->name('admin.dashboard');
    Route::put('/alert-status', [AlertStatusController::class, 'update'])->name('admin.alert-status.update');

    // Logistics CRUD
    Route::get('/logistics', [LogisticController::class, 'index'])->name('logistics.index');
    Route::post('/logistics', [LogisticController::class, 'store'])->name('logistics.store');
    Route::get('/logistics/{logistic}/edit', [LogisticController::class, 'edit'])->name('logistics.edit');
    Route::put('/logistics/{logistic}', [LogisticController::class, 'update'])->name('logistics.update');
    Route::delete('/logistics/{logistic}', [LogisticController::class, 'destroy'])->name('logistics.destroy');

    // Citizen Report Status & Deletion
    Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.update-status');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    // Emergency Contacts Management
    Route::post('/emergency-contacts', [EmergencyContactController::class, 'store'])->name('emergency-contacts.store');
    Route::put('/emergency-contacts/{contact}', [EmergencyContactController::class, 'update'])->name('emergency-contacts.update');
    Route::delete('/emergency-contacts/{contact}', [EmergencyContactController::class, 'destroy'])->name('emergency-contacts.destroy');

    // Admin Account Password Update
    Route::put('/password', [AuthController::class, 'updatePassword'])->name('admin.password.update');
});
