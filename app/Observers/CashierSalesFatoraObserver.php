<?php

namespace App\Observers;

use App\Models\CashierSalesFatora;

class CashierSalesFatoraObserver
{
  /**
   * Handle the CashierSalesFatora "deleting" event.
   */
  public function deleting(CashierSalesFatora $fatora): void
  {
    foreach ($fatora->items as $sale) {

      $salesPoint = $sale->cashier?->salesPoint;
      if ($salesPoint && $salesPoint->warehouse) {
        $warehouse = $salesPoint->warehouse;
        $variantInWh = $warehouse->productVariants()
          ->where('product_variant_id', $sale->product_variant_id)
          ->first();

        if ($variantInWh) {
          $warehouse->productVariants()->updateExistingPivot($sale->product_variant_id, [
            'amount' => $variantInWh->pivot->amount + $sale->quantity,
          ]);
        }
      }

      if ($sale->cashier) {
        $sale->cashier->decrement('daily_limit', $sale->full_price);
      }

      $sale->delete();
    }
  }
}
