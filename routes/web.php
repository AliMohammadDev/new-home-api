<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ShippingWarehouseController;
use App\Http\Controllers\Api\SupplierImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('welcome');
});


Route::get('/orders/{order}/print', [OrderController::class, 'print'])
  ->name('orders.print')
  ->middleware(['auth']);


Route::get('/shipping/print', [ShippingWarehouseController::class, 'print'])->name('shipping.print');


Route::get('/print-supplier-imports', [SupplierImportController::class, 'print'])
  ->name('supplier.print');
