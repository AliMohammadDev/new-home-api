<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Api\ProductVariantPackageController;
use App\Http\Controllers\Api\ReviewsController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\socialAuthController;
use App\Http\Controllers\Api\WishListController;


/*
|--------------------------------------------------------------------------
| Public API (NO AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['setLocale'])->group(function () {

  // Social Auth
  Route::get('/login-google', [socialAuthController::class, 'redirectToProvider']);
  Route::get('/auth/google/callback', [socialAuthController::class, 'handleCallback']);

  // Auth
  Route::post('register', [AuthController::class, 'register']);
  Route::post('login', [AuthController::class, 'login']);

  // Contact & Password
  Route::post('contact-us', [ContactController::class, 'send'])->middleware('throttle:5,1');
  Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
  Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);

  // Categories
  Route::get('categories', [CategoryController::class, 'index']);
  Route::get('categories/{category}', [CategoryController::class, 'show']);

  // Products
  Route::get('products', [ProductController::class, 'index']);
  Route::get('products/{product}', [ProductController::class, 'show']);
  Route::get('products-sliders', [ProductController::class, 'sliders']);

  // Product Variants
  Route::get('product-variants', [ProductVariantController::class, 'index']);
  Route::get('product-variants/{product_variant}', [ProductVariantController::class, 'show']);
  Route::get('variants-all/{limit?}', [ProductVariantController::class, 'allVariantsByLimit']);
  Route::get('variants-sliders', [ProductVariantController::class, 'slidersVariants']);
  Route::get('variants-category/{name}', [ProductVariantController::class, 'byVariantsCategoryName']);

  // Attributes
  Route::get('colors', [ColorController::class, 'index']);
  Route::get('sizes', [SizeController::class, 'index']);
  Route::get('materials', [MaterialController::class, 'index']);


  // Product Variant Packages
  Route::get(
    'product-variant-packages',
    [ProductVariantPackageController::class, 'index']
  );

  Route::get(
    'product-variant-packages/{product_variant_package}',
    [ProductVariantPackageController::class, 'show']
  );

  Route::get(
    'product-variants/{product_variant}/packages',
    [ProductVariantPackageController::class, 'byVariant']
  );

});


/*
|--------------------------------------------------------------------------
| Authenticated User API
|--------------------------------------------------------------------------
*/
Route::middleware(['setLocale', 'auth:sanctum'])->group(function () {

  // User
  Route::get('me', [AuthController::class, 'me']);
  Route::put('profile', [AuthController::class, 'updateProfile']);

  // Wishlist
  Route::apiResource('wishlists', WishListController::class)
    ->only(['index', 'store', 'destroy']);

  // Reviews
  Route::apiResource('reviews', ReviewsController::class)
    ->only(['index', 'store', 'update', 'destroy']);

  // Cart
  Route::apiResource('cart-items', CartItemController::class)
    ->only(['index', 'store', 'update', 'destroy']);
  Route::patch('cart-items/{cart_item}/increase', [CartItemController::class, 'increase']);
  Route::patch('cart-items/{cart_item}/decrease', [CartItemController::class, 'decrease']);

  // Checkout & Orders
  Route::apiResource('checkouts', CheckoutController::class);
  Route::apiResource('orders', OrderController::class);
});


/*
|--------------------------------------------------------------------------
| Admin API (AUTH + ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['setLocale', 'auth:sanctum', 'admin'])->group(function () {

  Route::apiResource('categories', CategoryController::class)
    ->except(['index', 'show']);

  Route::apiResource('products', ProductController::class)
    ->except(['index', 'show']);

  Route::apiResource('product-variants', ProductVariantController::class)
    ->except(['index', 'show']);

  Route::apiResource('colors', ColorController::class)
    ->except(['index']);

  Route::apiResource('sizes', SizeController::class)
    ->except(['index']);

  Route::apiResource('materials', MaterialController::class)
    ->except(['index']);

  Route::apiResource(
    'product-variant-packages',
    ProductVariantPackageController::class
  )->except(['index', 'show']);
});