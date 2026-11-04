<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTicketController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\TicketController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

// --------------------------------------------
// Autentikasi API (Mendapatkan Token untuk Postman / Mobile)
// --------------------------------------------
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'nullable|string',
    ]);

    $user = User::with('customer')->where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Email atau password salah.'],
        ]);
    }

    $deviceName = $request->device_name ?? 'mobile-app';
    $token = $user->createToken($deviceName)->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login berhasil.',
        'data' => [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'customer_id' => $user->customer_id,
                'customer' => $user->customer ? [
                    'id' => $user->customer->id,
                    'customer_code' => $user->customer->customer_code,
                    'name' => $user->customer->name,
                    'type' => $user->customer->type,
                    'area' => $user->customer->area,
                ] : null,
            ],
        ],
    ]);
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Logout berhasil. Token telah dihapus.',
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user()->load('customer');

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'customer_id' => $user->customer_id,
            'customer' => $user->customer ? [
                'id' => $user->customer->id,
                'customer_code' => $user->customer->customer_code,
                'name' => $user->customer->name,
                'type' => $user->customer->type,
                'area' => $user->customer->area,
            ] : null,
        ],
    ]);
});

// ============================================
// REST API NEXOPS (Untuk Diuji di Postman)
// ============================================

Route::middleware(['auth:sanctum', 'customer'])->prefix('my')->group(function () {
    Route::get('/tickets', [CustomerTicketController::class, 'index']);
    Route::post('/tickets', [CustomerTicketController::class, 'store']);
    Route::get('/tickets/{id}', [CustomerTicketController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'staff'])->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'apiStats']);
    Route::get('/tickets', [TicketController::class, 'apiIndex']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{id}', [TicketController::class, 'apiShow']);
    Route::put('/tickets/{id}', [TicketController::class, 'update']);
    Route::patch('/tickets/{id}', [TicketController::class, 'update']);
    Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus']);
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);

    Route::get('/technicians', [TechnicianController::class, 'index']);
    Route::get('/technicians/{technician}', [TechnicianController::class, 'show']);
    Route::post('/technicians', [TechnicianController::class, 'store']);
    Route::put('/technicians/{technician}', [TechnicianController::class, 'update']);
    Route::patch('/technicians/{technician}/status', [TechnicianController::class, 'updateStatus']);
    Route::delete('/technicians/bulk', [TechnicianController::class, 'bulkDestroy']);
    Route::delete('/technicians/{technician}', [TechnicianController::class, 'destroy']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::post('/customers/{customer}/users', [CustomerController::class, 'linkUser'])->middleware('admin');
    Route::delete('/customers/{customer}/users/{user}', [CustomerController::class, 'unlinkUser'])->middleware('admin');
    Route::put('/customers/{customer}', [CustomerController::class, 'update']);
    Route::patch('/customers/{customer}/status', [CustomerController::class, 'updateStatus']);
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);

    Route::post('/tickets/{ticket}/assignments', [AssignmentController::class, 'store']);
    Route::delete('/tickets/{ticket}/assignments/{assignment}', [AssignmentController::class, 'destroy']);
});
