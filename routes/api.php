<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\userController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Item;
use App\Http\Resources\ItemApiResource;
use App\Http\Resources\CategoryApiResource;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//here we need log-in
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

});


/// Dashboard Apis
Route::get('/items', function () {
    return ItemApiResource::collection(Item::with('category')->get());
});



Route::middleware(['auth:sanctum','admin'])->prefix('admin')->group(function () {


    /// show the status and counts on main page at dashboard
    Route::get('stats',[AdminController::class,'dashboardStats']);



    /// show the top-selling items
    Route::get('top_selling',[ItemController::class,'topSelling']);


    /// CRUD operations on the users
    Route::apiResource('users', UserController::class);
    /// CRUD operation on items
    Route::apiResource('items', ItemController::class);
    /// CRUD operations on categories
    Route::apiResource('category', CategoryController::class);

});




