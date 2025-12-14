<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\ReviewsController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\WishListController;
use Illuminate\Support\Facades\Route;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

  // My profile
  Route::get('/me', [AuthController::class, 'me']);

  // WishList
  Route::apiResource('wishlists', WishListController::class)
    ->only(['index', 'store', 'destroy']);
  // Reviews
  Route::apiResource('reviews', ReviewsController::class)
    ->only(['index', 'store', 'update', 'destroy']);

  // CartItems
  Route::apiResource('cart-items', CartItemController::class)
    ->only(['index', 'store', 'update', 'destroy']);
  Route::patch('/cart-items/{cart_item}/increase', [CartItemController::class, 'increase']);
  Route::patch('/cart-items/{cart_item}/decrease', [CartItemController::class, 'decrease']);

  // Checkouts
  Route::apiResource('checkouts', CheckoutController::class);
  // Orders
  Route::apiResource('orders', OrderController::class);

  // Products Variants
  Route::apiResource('product-variants', ProductVariantController::class);



});

Route::apiResource('categories', CategoryController::class);

Route::apiResource('products', ProductController::class);
Route::get('/products-sliders', [ProductController::class, 'sliders']);
Route::get('products-all/{limit?}', [ProductController::class, 'allByLimit']);


Route::apiResource('colors', ColorController::class);
Route::apiResource('sizes', SizeController::class);
Route::apiResource('materials', MaterialController::class);