<?php

namespace App\Observers;

use App\Models\CashierSale;
use App\Models\CashierSalesFatora;

class CashierSaleObserver
{


  public function creating(CashierSale $cashierSale): void
  {
    $fatora = CashierSalesFatora::firstOrCreate(
      [
        'sales_point_cashier_id' => $cashierSale->sales_point_cashier_id,
        'date' => now()->toDateString(),
      ],
      [
        'full_price' => 0,
      ]
    );

    $cashierSale->cashier_sales_fatora_id = $fatora->id;
  }


  /**
   * Handle the CashierSale "created" event.
   */
  public function created(CashierSale $cashierSale): void
  {
    $cashierSale->fatora->increment('full_price', $cashierSale->full_price);

    $cashier = $cashierSale->cashier;
    $cashier->increment('daily_limit', $cashierSale->full_price);


    $salesPoint = $cashier->salesPoint;
    if ($salesPoint && $salesPoint->warehouse) {
      $warehouse = $salesPoint->warehouse;

      $variantInWarehouse = $warehouse->productVariants()
        ->where('product_variant_id', $cashierSale->product_variant_id)
        ->first();

      if ($variantInWarehouse) {
        $currentAmount = $variantInWarehouse->pivot->amount;
        $warehouse->productVariants()->updateExistingPivot($cashierSale->product_variant_id, [
          'amount' => $currentAmount - $cashierSale->quantity,
        ]);
      }
    }



  }

  /**
   * Handle the CashierSale "updated" event.
   */
  public function updated(CashierSale $cashierSale): void
  {
    if ($cashierSale->wasChanged('full_price')) {
      $oldFullPrice = $cashierSale->getOriginal('full_price');
      $newFullPrice = $cashierSale->full_price;
      $priceDifference = $newFullPrice - $oldFullPrice;

      $cashierSale->fatora->increment('full_price', $priceDifference);

      $cashier = $cashierSale->cashier;
      if ($cashier) {
        $cashier->increment('daily_limit', $priceDifference);
      }
    }

    if ($cashierSale->wasChanged(['quantity', 'product_variant_id'])) {
      $salesPoint = $cashierSale->cashier?->salesPoint;
      if ($salesPoint && $salesPoint->warehouse) {
        $warehouse = $salesPoint->warehouse;

        if ($cashierSale->wasChanged('product_variant_id')) {
          $oldVariantId = $cashierSale->getOriginal('product_variant_id');
          $oldQuantity = $cashierSale->getOriginal('quantity');

          $oldVariantInWh = $warehouse->productVariants()->where('product_variant_id', $oldVariantId)->first();
          if ($oldVariantInWh) {
            $warehouse->productVariants()->updateExistingPivot($oldVariantId, [
              'amount' => $oldVariantInWh->pivot->amount + $oldQuantity,
            ]);
          }

          $newVariantInWh = $warehouse->productVariants()->where('product_variant_id', $cashierSale->product_variant_id)->first();
          if ($newVariantInWh) {
            $warehouse->productVariants()->updateExistingPivot($cashierSale->product_variant_id, [
              'amount' => $newVariantInWh->pivot->amount - $cashierSale->quantity,
            ]);
          }
        } else {
          $oldQuantity = $cashierSale->getOriginal('quantity');
          $newQuantity = $cashierSale->quantity;
          $quantityDifference = $newQuantity - $oldQuantity;

          $variantInWh = $warehouse->productVariants()->where('product_variant_id', $cashierSale->product_variant_id)->first();
          if ($variantInWh) {
            $warehouse->productVariants()->updateExistingPivot($cashierSale->product_variant_id, [
              'amount' => $variantInWh->pivot->amount - $quantityDifference,
            ]);
          }
        }
      }
    }

  }

  /**
   * Handle the CashierSale "deleted" event.
   */
  public function deleted(CashierSale $cashierSale): void
  {
    $cashierSale->fatora->decrement('full_price', $cashierSale->full_price);
    $cashierSale->cashier?->decrement('daily_limit', $cashierSale->full_price);

    $salesPoint = $cashierSale->cashier?->salesPoint;
    if ($salesPoint && $salesPoint->warehouse) {
      $variantInWh = $salesPoint->warehouse->productVariants()
        ->where('product_variant_id', $cashierSale->product_variant_id)
        ->first();

      if ($variantInWh) {
        $salesPoint->warehouse->productVariants()->updateExistingPivot($cashierSale->product_variant_id, [
          'amount' => $variantInWh->pivot->amount + $cashierSale->quantity,
        ]);
      }
    }
  }

  /**
   * Handle the CashierSale "restored" event.
   */
  public function restored(CashierSale $cashierSale): void
  {
    //
  }

  /**
   * Handle the CashierSale "force deleted" event.
   */
  public function forceDeleted(CashierSale $cashierSale): void
  {
    //
  }
}