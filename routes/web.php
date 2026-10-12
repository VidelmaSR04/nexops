<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/tickets', function () {
    return view('tickets');
})->middleware(['auth', 'verified'])->name('tickets.index');

Route::get('/inventory', function () {
    return view('inventory');
})->middleware(['auth', 'verified'])->name('inventory.index');

Route::get('/assets', function () {
    return view('assets');
})->middleware(['auth', 'verified'])->name('assets.index');

Route::get('/warehouse', function () {
    return view('warehouse');
})->middleware(['auth', 'verified'])->name('warehouse.index');

Route::view('/technicians', 'technicians')
    ->middleware(['auth', 'verified'])
    ->name('technicians.index');

Route::view('/customers', 'customers')
    ->middleware(['auth', 'verified'])
    ->name('customers.index');

Route::view('/reports', 'reports')
    ->middleware(['auth', 'verified'])
    ->name('reports.index');

    Route::view('/settings', 'settings')
    ->middleware(['auth', 'verified'])
    ->name('settings.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
