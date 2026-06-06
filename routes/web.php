<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MyIncidentController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/alerts/{alert}/incident', [IncidentController::class, 'store'])
        ->name('alerts.incident');

    Route::post('/incidents/{incident}/close', [IncidentController::class, 'close'])
        ->name('incidents.close');

    Route::post('/incidents/{incident}/logs', [IncidentLogController::class, 'store'])
        ->name('incidents.logs.store');

    Route::get('/reports/operational', [ReportController::class, 'operational'])
        ->name('reports.operational');

    Route::get('/mis-incidencias', [MyIncidentController::class, 'index'])
        ->name('my-incidents.index');
});
