<?php

namespace App\Observers;

use App\Models\ShippingWarehouse;

class ShippingWarehouseObserver
{
  /**
   * Handle the ShippingWarehouse "created" event.
   */
  public function created(ShippingWarehouse $shippingWarehouse): void
  {
    $variant = $shippingWarehouse->productVariant;
    if ($variant) {
      $variant->decrement('stock_quantity', $shippingWarehouse->amount);
    }
  }

  /**
   * Handle the ShippingWarehouse "updated" event.
   */
  public function updated(ShippingWarehouse $shippingWarehouse): void
  {
    if ($shippingWarehouse->wasChanged('amount')) {
      $oldAmount = $shippingWarehouse->getOriginal('amount');
      $newAmount = $shippingWarehouse->amount;
      $difference = $newAmount - $oldAmount;

      $variant = $shippingWarehouse->productVariant;
      if ($variant) {
        $variant->decrement('stock_quantity', $difference);
      }
    }
  }

  /**
   * Handle the ShippingWarehouse "deleted" event.
   */
  public function deleted(ShippingWarehouse $shippingWarehouse): void
  {
    $variant = $shippingWarehouse->productVariant;
    if ($variant) {
      $variant->increment('stock_quantity', $shippingWarehouse->amount);
    }
  }

  /**
   * Handle the ShippingWarehouse "restored" event.
   */
  public function restored(ShippingWarehouse $shippingWarehouse): void
  {
    //
  }

  /**
   * Handle the ShippingWarehouse "force deleted" event.
   */
  public function forceDeleted(ShippingWarehouse $shippingWarehouse): void
  {
    //
  }
}