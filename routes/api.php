<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\ReviewsController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\WishListController;
use Illuminate\Support\Facades\Route;



// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function () {

//   // My profile
//   Route::get('/me', [AuthController::class, 'me']);
//   // Update profile
//   Route::put('/profile', [AuthController::class, 'updateProfile']);

//   // WishList
//   Route::apiResource('wishlists', WishListController::class)
//     ->only(['index', 'store', 'destroy']);
//   // Reviews
//   Route::apiResource('reviews', controller: ReviewsController::class)
//     ->only(['index', 'store', 'update', 'destroy']);

//   // CartItems
//   Route::apiResource('cart-items', CartItemController::class)
//     ->only(['index', 'store', 'update', 'destroy']);
//   Route::patch('/cart-items/{cart_item}/increase', [CartItemController::class, 'increase']);
//   Route::patch('/cart-items/{cart_item}/decrease', [CartItemController::class, 'decrease']);

//   // Checkouts
//   Route::apiResource('checkouts', CheckoutController::class);
//   // Orders
//   Route::apiResource('orders', OrderController::class);

// });

// Route::apiResource('categories', CategoryController::class);

// Route::apiResource('products', ProductController::class);
// // Products Variants
// Route::apiResource('product-variants', ProductVariantController::class);

// Route::get('variants-all/{limit?}', [ProductVariantController::class, 'allVariantsByLimit']);
// Route::get('variants-sliders', [ProductVariantController::class, 'slidersVariants']);
// Route::get('variants-category/{name}', [ProductVariantController::class, 'byVariantsCategoryName']);


// Route::get('/products-sliders', [ProductController::class, 'sliders']);
// // Route::get('products-all/{limit?}', [ProductController::class, 'allByLimit']);
// // Route::get('products-category/{name}', [ProductController::class, 'byCategoryName']);

// Route::apiResource('colors', ColorController::class);
// Route::apiResource('sizes', SizeController::class);
// Route::apiResource('materials', MaterialController::class);


// Route::post('/contact-us', [ContactController::class, 'send'])
//   ->middleware('throttle:5,1'); // 5 requests per minute


// Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
// Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);



Route::middleware(['setLocale'])->group(function () {
  Route::post('/register', [AuthController::class, 'register']);
  Route::post('/login', [AuthController::class, 'login']);

  Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::apiResource('wishlists', WishListController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('reviews', ReviewsController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('cart-items', CartItemController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/cart-items/{cart_item}/increase', [CartItemController::class, 'increase']);
    Route::patch('/cart-items/{cart_item}/decrease', [CartItemController::class, 'decrease']);
    Route::apiResource('checkouts', CheckoutController::class);
    Route::apiResource('orders', OrderController::class);
  });

  Route::apiResource('categories', CategoryController::class);
  Route::apiResource('products', ProductController::class);
  Route::apiResource('product-variants', ProductVariantController::class);
  Route::get('variants-all/{limit?}', [ProductVariantController::class, 'allVariantsByLimit']);
  Route::get('variants-sliders', [ProductVariantController::class, 'slidersVariants']);
  Route::get('variants-category/{name}', [ProductVariantController::class, 'byVariantsCategoryName']);
  Route::get('/products-sliders', [ProductController::class, 'sliders']);
  Route::apiResource('colors', ColorController::class);
  Route::apiResource('sizes', SizeController::class);
  Route::apiResource('materials', MaterialController::class);
  Route::post('/contact-us', [ContactController::class, 'send'])->middleware('throttle:5,1');
  Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
  Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
});
