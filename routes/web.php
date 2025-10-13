<?php

use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/monitoring/login');

Route::middleware('guest')->group(function () {
    Route::get('/monitoring/login', [MonitoringController::class, 'showLoginForm'])->name('monitoring.login');
    Route::post('/monitoring/login', [MonitoringController::class, 'login'])->name('monitoring.login.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/monitoring', [MonitoringController::class, 'dashboard'])->name('monitoring.dashboard');
    Route::post('/monitoring/logout', [MonitoringController::class, 'logout'])->name('monitoring.logout');
});
