<?php


use App\Http\Middleware\CompanyMiddleware;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - نسخة المتجر النهائية والتنفيذية
|--------------------------------------------------------------------------
*/

// 🌍 1. مسارات عامة (لا تتطلب تسجيل دخول)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// 🔒 2. مسارات محمية عامة (تحتاج فقط تسجيل دخول بغض النظر عن الدور)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

});


// 🛒 3. مسارات محمية خاصة بالبائعين (تعتمد على الـ Middleware الخاص بك)
Route::middleware(['auth:sanctum', CompanyMiddleware::class])->group(function () {

    Route::post('/items', [ItemController::class, 'store']);          // إضافة عنصر جديد
    Route::post('/items/update', [ItemController::class, 'update']);   // تعديل عنصر
    Route::delete('/items/{id}', [ItemController::class, 'destroy']); // حذف عنصر

});





// 👮 4. مسارات محمية خاصة بمدير النظام (Admin)
Route::middleware(['auth:sanctum', 'AdminMiddleware'])->group(function () {

    Route::post('/admin/accept-item', [AdminController::class, 'acceptItem']);
    Route::post('/admin/reject-item', [AdminController::class, 'rejectItem']);

});
