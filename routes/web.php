<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('welcome');
});


Route::get('/orders/{order}/print', [OrderController::class, 'print'])
  ->name('orders.print')
  ->middleware(['auth']);