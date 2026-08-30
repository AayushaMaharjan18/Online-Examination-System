<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\FooterController;
use App\Http\Controllers\Api\V1\HomepageController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\ShippingController;
use App\Http\Controllers\Api\V1\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// V1 API Routes
Route::prefix('v1')->group(function () {

    // Public routes
    Route::get('homepage', [HomepageController::class, 'index']);

    // Products
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/featured', [ProductController::class, 'featured']);
    Route::get('products/{slug}', [ProductController::class, 'show']);
    Route::get('products/{product}/related', [ProductController::class, 'related']);

    // Brands
    Route::get('brands', [BrandController::class, 'index']);
    Route::get('brands/featured', [BrandController::class, 'featured']);
    Route::get('brands/{slug}', [BrandController::class, 'show']);

    // Footer
    Route::get('footer', [FooterController::class, 'index']);

    // Shipping
    Route::get('shipping/districts', [ShippingController::class, 'districts']);

    // Categories
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{slug}', [CategoryController::class, 'show']);

    // Auth
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/user', [AuthController::class, 'user']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('user/profile', [AuthController::class, 'profile']);
        Route::put('user/profile', [AuthController::class, 'updateProfile']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::post('orders', [OrderController::class, 'store']);
        Route::post('payments/khalti/verify', [PaymentController::class, 'khaltiVerify']);
        Route::post('payments/esewa/init', [PaymentController::class, 'esewaInit']);
        Route::post('payments/esewa/verify', [PaymentController::class, 'esewaVerify']);
        Route::post('reviews', [ReviewController::class, 'store']);
        Route::get('reviews/mine', [ReviewController::class, 'mine']);
        Route::put('reviews/{review}', [ReviewController::class, 'update']);
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);
        Route::get('products/{product}/review-eligibility', [ReviewController::class, 'eligibility']);
        Route::get('wishlist', [WishlistController::class, 'index']);
        Route::post('wishlist', [WishlistController::class, 'store']);
        Route::get('wishlist/{product}', [WishlistController::class, 'show']);
        Route::delete('wishlist/{product}', [WishlistController::class, 'destroy']);
    });

    Route::get('reviews', [ReviewController::class, 'publicIndex']);
    Route::get('products/{product}/reviews', [ReviewController::class, 'index']);
});

// Sanctum user endpoint
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');