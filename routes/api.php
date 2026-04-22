<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// هذا الرابط سيكون: http://127.0.0.1:8000/api/register
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
});


