<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/tickets', [TicketController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('tickets.index');

Route::post('/tickets', [TicketController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('tickets.store');

Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus'])
    ->middleware(['auth', 'verified'])
    ->name('tickets.update-status');

Route::resource('tickets', TicketController::class)->except([
    'index', 'store', 'updateStatus'
])->middleware(['auth', 'verified']);

Route::get('/inventory', [InventoryController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('inventory.index');

Route::get('/assets', [AssetController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('assets.index');

Route::get('/warehouse', [WarehouseController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('warehouse.index');

Route::get('/technicians', [TechnicianController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('technicians.index');

Route::get('/customers', [CustomerController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('customers.index');

Route::post('/customers', [CustomerController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('customers.store');

Route::view('/reports', 'reports')
    ->middleware(['auth', 'verified'])
    ->name('reports.index');

Route::get('/settings', [SettingController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('settings.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
