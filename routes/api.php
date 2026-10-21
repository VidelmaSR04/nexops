<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ============================================
// REST API NEXOPS (Untuk Diuji di Postman)
// ============================================

// 1. Dashboard API
Route::get('/dashboard/stats', [DashboardController::class, 'apiStats']);

// 2. Tickets API
Route::get('/tickets', [TicketController::class, 'apiIndex']);
Route::get('/tickets/{id}', [TicketController::class, 'apiShow']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/technicians', [TechnicianController::class, 'index']);
    Route::get('/technicians/{technician}', [TechnicianController::class, 'show']);
    Route::post('/technicians', [TechnicianController::class, 'store']);
    Route::put('/technicians/{technician}', [TechnicianController::class, 'update']);
    Route::patch('/technicians/{technician}/status', [TechnicianController::class, 'updateStatus']);
    Route::delete('/technicians/{technician}', [TechnicianController::class, 'destroy']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{customer}', [CustomerController::class, 'update']);
    Route::patch('/customers/{customer}/status', [CustomerController::class, 'updateStatus']);
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);

    Route::post('/tickets/{ticket}/assignments', [AssignmentController::class, 'store']);
    Route::delete('/tickets/{ticket}/assignments/{assignment}', [AssignmentController::class, 'destroy']);
});
