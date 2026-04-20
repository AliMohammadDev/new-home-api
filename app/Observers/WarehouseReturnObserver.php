<?php

namespace App\Observers;

use App\Models\ShippingWarehouse;
use App\Models\WarehouseReturn;

class WarehouseReturnObserver
{

  public function created(WarehouseReturn $warehouseReturn): void
  {
    $variant = $warehouseReturn->productVariant;
    if ($variant) {
      $variant->increment('stock_quantity', $warehouseReturn->amount);
    }
    $this->updateSubWarehouseStock($warehouseReturn, $warehouseReturn->amount);
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
      $this->updateSubWarehouseStock($warehouseReturn, $difference);
    }
  }


  public function deleted(WarehouseReturn $warehouseReturn): void
  {
    $variant = $warehouseReturn->productVariant;
    if ($variant) {
      $variant->decrement('stock_quantity', $warehouseReturn->amount);
    }
    $this->updateSubWarehouseStock($warehouseReturn, -$warehouseReturn->amount);
  }



  protected function updateSubWarehouseStock(WarehouseReturn $return, $amount)
  {
    $stockEntry = ShippingWarehouse::where('warehouse_id', $return->warehouse_id)
      ->where('product_variant_id', $return->product_variant_id)
      ->first();

    // if ($stockEntry) {
    //   $stockEntry->decrement('amount', $amount);
    // }

    if ($stockEntry) {
      ShippingWarehouse::withoutEvents(function () use ($stockEntry, $amount) {
        $stockEntry->decrement('amount', $amount);
      });
    }
  }

}
