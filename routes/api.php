<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SizeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('colors', ColorController::class);
Route::apiResource('sizes', SizeController::class);
Route::apiResource('materials', MaterialController::class);


Route::post('products/{product}/attach-color', [ProductController::class, 'attachColor']);
Route::post('products/{product}/detach-color', [ProductController::class, 'detachColor']);

Route::post('products/{product}/attach-size', [ProductController::class, 'attachSize']);
Route::post('products/{product}/detach-size', [ProductController::class, 'detachSize']);

Route::post('products/{product}/attach-material', [ProductController::class, 'attachMaterial']);
Route::post('products/{product}/detach-material', [ProductController::class, 'detachMaterial']);

