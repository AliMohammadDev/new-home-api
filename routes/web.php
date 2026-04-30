<?php

use App\Http\Controllers\Api\FatoraPrintController;
use App\Http\Controllers\Api\FinancialReportController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ShippingWarehouseController;
use App\Http\Controllers\Api\SupplierImportController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return redirect('/admin');
});

Route::get('/reset-database', function () {
  if (app()->environment('local')) {

    Artisan::call('migrate:fresh', [
      '--seed' => true,
      '--force' => true,
    ]);

    return response()->json([
      'status' => 'success',
      'message' => 'Database has been cleared, rebuilt, and seeded successfully!',
      'action' => 'You can now go to the dashboard',
      'url' => url('/admin')
    ]);
  }

  return response()->json([
    'status' => 'error',
    'message' => 'Sorry, this command is only available in the Local development environment.'
  ], 403);
});


Route::get('/orders/{order}/print', [OrderController::class, 'print'])
  ->name('orders.print')
  ->middleware(['auth']);


Route::get('/shipping/print', [ShippingWarehouseController::class, 'print'])->name('shipping.print');


Route::get('/print-supplier-imports', [SupplierImportController::class, 'print'])
  ->name('supplier.print');

Route::get('/print-supplier-product-imports', [SupplierImportController::class, 'showProductImport'])
  ->name('product.import.print');


Route::get('/print-fatora', [FatoraPrintController::class, 'print'])
  ->name('fatora.print');

Route::get('/print-financial-report', [FinancialReportController::class, 'print'])
  ->name('reports.print.financial')
  ->middleware(['auth']);

Route::get('/print-product-report', [FinancialReportController::class, 'printProductReport'])
  ->name('reports.print.products');
