<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AdminController;



// هذا الرابط سيكون: http://127.0.0.1:8000/api/register
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
});
Route::middleware(['auth:sanctum', 'CompanyMiddleware'])->group(function () {

Route::post('/items', [ItemController::class, 'store']);

});
//---------------------------------------------------------------
Route::middleware(['auth:sanctum', 'AdminMiddleware'])->group(function () {

Route::post('/admin/accept-item', [AdminController::class, 'acceptItem']);
Route::post('/admin/reject-item', [AdminController::class, 'rejectItem']);

});


