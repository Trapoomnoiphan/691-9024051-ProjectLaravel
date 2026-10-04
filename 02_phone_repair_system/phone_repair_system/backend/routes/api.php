<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\TicketApiController;

/*
|--------------------------------------------------------------------------
| API Routes (Laravel Sanctum Token Authenticated)
|--------------------------------------------------------------------------
*/

// Public Authentication Endpoints
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

// Public Tracking and Repair Types
Route::get('/repair-types', [TicketApiController::class, 'getRepairTypes']);
Route::get('/tickets/{code}', [TicketApiController::class, 'getTicketByCode']);
Route::get('/stats', [TicketApiController::class, 'getStats']);

// Authenticated Endpoints (Bearer Token)
Route::middleware('auth.token')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);

    // Tickets
    Route::get('/tickets', [TicketApiController::class, 'indexTickets']);
    Route::post('/tickets', [TicketApiController::class, 'createTicket']);

    // Admin Only Endpoints
    Route::middleware('role:admin')->group(function () {
        Route::post('/repair-types', [TicketApiController::class, 'storeRepairType']);
        Route::post('/tickets/{id}/status', [TicketApiController::class, 'updateStatus']);
    });
});
