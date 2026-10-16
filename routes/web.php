<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssignmentController;
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

Route::post('/inventory', [InventoryController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('inventory.store');

Route::put('/inventory/{sku}', [InventoryController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('inventory.update');

Route::delete('/inventory/{sku}', [InventoryController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('inventory.destroy');

Route::get('/assets', [AssetController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('assets.index');

Route::post('/assets', [AssetController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('assets.store');

Route::put('/assets/{asset:asset_code}', [AssetController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('assets.update');

Route::patch('/assets/{asset:asset_code}/status', [AssetController::class, 'updateStatus'])
    ->middleware(['auth', 'verified'])
    ->name('assets.update-status');

Route::post('/assets/{asset:asset_code}/maintenance', [AssetController::class, 'storeMaintenance'])
    ->middleware(['auth', 'verified'])
    ->name('assets.maintenance.store');

Route::delete('/assets/{asset:asset_code}', [AssetController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('assets.destroy');

Route::get('/warehouse', [WarehouseController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('warehouse.index');

Route::post('/warehouse', [WarehouseController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('warehouse.store');

Route::put('/warehouse/{warehouse:code}', [WarehouseController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('warehouse.update');

Route::patch('/warehouse/{warehouse:code}/status', [WarehouseController::class, 'updateStatus'])
    ->middleware(['auth', 'verified'])
    ->name('warehouse.update-status');

Route::delete('/warehouse/{warehouse:code}', [WarehouseController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('warehouse.destroy');

Route::post('/warehouse/{warehouse:code}/movements', [WarehouseController::class, 'storeMovement'])
    ->middleware(['auth', 'verified'])
    ->name('warehouse.movements.store');

Route::get('/technicians', [TechnicianController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('technicians.index');

Route::get('/technicians/{technician}', [TechnicianController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('technicians.show');

Route::post('/technicians', [TechnicianController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('technicians.store');

Route::put('/technicians/{technician}', [TechnicianController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('technicians.update');

Route::patch('/technicians/{technician}/status', [TechnicianController::class, 'updateStatus'])
    ->middleware(['auth', 'verified'])
    ->name('technicians.update-status');

Route::delete('/technicians/{technician}', [TechnicianController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('technicians.destroy');

Route::post('/tickets/{ticket}/assignments', [AssignmentController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('tickets.assignments.store');

Route::delete('/tickets/{ticket}/assignments/{assignment}', [AssignmentController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('tickets.assignments.destroy');

Route::get('/customers', [CustomerController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('customers.index');

Route::get('/customers/{customer}', [CustomerController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('customers.show');

Route::post('/customers', [CustomerController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('customers.store');

Route::put('/customers/{customer}', [CustomerController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('customers.update');

Route::patch('/customers/{customer}/status', [CustomerController::class, 'updateStatus'])
    ->middleware(['auth', 'verified'])
    ->name('customers.update-status');

Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('customers.destroy');

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
