<?php

namespace App\Observers;

use App\Models\WarehouseReturn;

class WarehouseReturnObserver
{

  public function created(WarehouseReturn $warehouseReturn): void
  {
    $variant = $warehouseReturn->productVariant;
    if ($variant) {
      $variant->increment('stock_quantity', $warehouseReturn->amount);
    }
  }


  public function updated(WarehouseReturn $warehouseReturn): void
  {
    if ($warehouseReturn->wasChanged('amount')) {
      $oldAmount = $warehouseReturn->getOriginal('amount');
      $newAmount = $warehouseReturn->amount;
      $difference = $newAmount - $oldAmount;

      $variant = $warehouseReturn->productVariant;
      if ($variant) {
        $variant->increment('stock_quantity', $difference);
      }
    }
  }


  public function deleted(WarehouseReturn $warehouseReturn): void
  {
    $variant = $warehouseReturn->productVariant;
    if ($variant) {
      $variant->decrement('stock_quantity', $warehouseReturn->amount);
    }
  }

}