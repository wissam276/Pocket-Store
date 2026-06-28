<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\userController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CouponController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Item;
use App\Http\Resources\ItemApiResource;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//here we need log-in
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    /// forget password
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    /// reset password
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
    Route::put('/profile/update', [AuthController::class, 'updateProfile']);
    Route::get('/my-orders', [OrderController::class, 'getUserOrders']);
    /// CRUD operations on basket
    Route::apiResource('BasketOfCustomer', BasketController::class);
    // اذا اردنا تعديل عنصر معين في السلة
    //    Route::put('/BasketOfCustomer/{id}', [BasketController::class, 'update']);
    //    Route::delete('/BasketOfCustomer/{id}', [BasketController::class, 'destroy']);
    /// CRUD operations on order
    Route::apiResource('orderOfCustomer', OrderController::class);
    ///make order from basket
    Route::post('checkout',[OrderController::class,'checkout']);
});


//==================================================================================
//General Accessibility

        Route::get('/items', function () {
            return ItemApiResource::collection(Item::with('category')->get());
        });


        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('topSellingItems', [ItemController::class, 'topSellingItems']);
        Route::get('itemsByCategory',[ItemController::class, 'itemsByCategory']);
        Route::get('search', [ItemController::class, 'search']);
        Route::get('filter', [ItemController::class, 'filteringItem']);
        Route::get('itemDetails', [ItemController::class, 'ItemDetails']);
        Route::get('itemsWithSales', [ItemController::class, 'itemsWithSales']);
        Route::get('coupons', [CouponController::class, 'index']);


//==================================================================================


/// Dashboard Apis
Route::middleware(['auth:sanctum','admin'])->prefix('admin')->group(function () {

        /// show the status and counts on main page at dashboard
        Route::get('stats',[AdminController::class,'dashboardStats']);
        /// show the top-selling items
        Route::get('topSelling',[ItemController::class,'topSelling']);
        /// show the items With Sales
        Route::get('itemWithSales',[ItemController::class,'ItemsWithSales']);
        /// CRUD operations on the users
        Route::apiResource('/customer', UserController::class);
        /// edit status of user between active & block
        Route::patch('/customer/{id}/toggle-status', [AdminController::class, 'toggleUserStatus']);
        /// CRUD operation on items
        Route::apiResource('items', ItemController::class);
        /// CRUD operations on categories
        Route::apiResource('category', CategoryController::class);
        /// show orders of customer
        Route::get('/customers/{customer}/orders',[UserController::class,'orders']);
        /// CRUD operations on orders
        Route::apiResource('orders',OrderController::class);
        /// CRUD operations on coupons and offers
        Route::get('coupons', [CouponController::class, 'adminIndex']);
        Route::post('coupons', [CouponController::class, 'store']);
        Route::put('coupons/{coupon}', [CouponController::class, 'update']);
        Route::patch('coupons/{coupon}', [CouponController::class, 'update']);
        Route::delete('coupons/{coupon}', [CouponController::class, 'destroy']);
});
// wishlist apis
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist']);
    Route::post('/wishlist/remove', [WishlistController::class, 'removeFromWishlist']);
    Route::get('/wishlist', [WishlistController::class, 'viewWishlist']);
    Route::post('/wishlist/clear', [WishlistController::class, 'clearWishlist']);
    Route::post('/wishlist/move-to-basket', [WishlistController::class, 'moveToBasket']);
});


////// testtt
