<?php

use App\Http\Controllers\DashboardController;
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
