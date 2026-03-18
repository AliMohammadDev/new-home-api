<?php

namespace App\Observers;

use App\Models\CashierReturnFatora;
use App\Models\CashierSalesReturn;

class CashierSalesReturnObserver
{


  public function creating(CashierSalesReturn $cashierSale): void
  {
    $fatora = CashierReturnFatora::firstOrCreate(
      [
        'sales_point_cashier_id' => $cashierSale->sales_point_cashier_id,
        'date' => now()->toDateString(),
      ],
      [
        'full_price' => 0,
      ]
    );

    $cashierSale->cashier_return_fatora_id = $fatora->id;
  }

  /**
   * Handle the CashierSalesReturn "created" event.
   */
  public function created(CashierSalesReturn $cashierSalesReturn): void
  {
    $cashierSalesReturn->fatora->increment('full_price', $cashierSalesReturn->full_price);

    $cashier = $cashierSalesReturn->cashier;
    $cashier->decrement('daily_limit', $cashierSalesReturn->full_price);


    $salesPoint = $cashier->salesPoint;
    if ($salesPoint && $salesPoint->warehouse) {
      $warehouse = $salesPoint->warehouse;

      $variantInWarehouse = $warehouse->productVariants()
        ->where('product_variant_id', $cashierSalesReturn->product_variant_id)
        ->first();



      if ($variantInWarehouse) {
        $currentAmount = $variantInWarehouse->pivot->amount;
        $warehouse->productVariants()->updateExistingPivot($cashierSalesReturn->product_variant_id, [
          'amount' => $currentAmount + $cashierSalesReturn->quantity,
        ]);
      } else {
        $warehouse->productVariants()->attach($cashierSalesReturn->product_variant_id, [
          'amount' => $cashierSalesReturn->quantity,
          'arrival_time' => now(),
          'user_id' => auth()->id() ?? $cashierSalesReturn->cashier?->user_id,
        ]);
      }

    }

  }

  /**
   * Handle the CashierSalesReturn "updated" event.
   */
  public function updated(CashierSalesReturn $cashierSalesReturn): void
  {
    if ($cashierSalesReturn->wasChanged('full_price')) {
      $oldFullPrice = $cashierSalesReturn->getOriginal('full_price');
      $newFullPrice = $cashierSalesReturn->full_price;
      $priceDifference = $newFullPrice - $oldFullPrice;

      $cashierSalesReturn->fatora->increment('full_price', $priceDifference);

      $cashier = $cashierSalesReturn->cashier;
      if ($cashier) {
        $cashier->decrement('daily_limit', $priceDifference);
      }
    }

    if ($cashierSalesReturn->wasChanged(['quantity', 'product_variant_id'])) {
      $salesPoint = $cashierSalesReturn->cashier?->salesPoint;
      if ($salesPoint && $salesPoint->warehouse) {
        $warehouse = $salesPoint->warehouse;

        if ($cashierSalesReturn->wasChanged('product_variant_id')) {
          $oldVariantId = $cashierSalesReturn->getOriginal('product_variant_id');
          $oldQuantity = $cashierSalesReturn->getOriginal('quantity');
          $oldVariantInWh = $warehouse->productVariants()->where('product_variant_id', $oldVariantId)->first();
          if ($oldVariantInWh) {
            $warehouse->productVariants()->updateExistingPivot($oldVariantId, [
              'amount' => $oldVariantInWh->pivot->amount - $oldQuantity,
            ]);
          }

          $newVariantInWh = $warehouse->productVariants()->where('product_variant_id', $cashierSalesReturn->product_variant_id)->first();
          if ($newVariantInWh) {
            $warehouse->productVariants()->updateExistingPivot($cashierSalesReturn->product_variant_id, [
              'amount' => $newVariantInWh->pivot->amount + $cashierSalesReturn->quantity,
            ]);
          }
        } else {
          $oldQuantity = $cashierSalesReturn->getOriginal('quantity');
          $newQuantity = $cashierSalesReturn->quantity;
          $quantityDifference = $newQuantity - $oldQuantity;

          $variantInWh = $warehouse->productVariants()->where('product_variant_id', $cashierSalesReturn->product_variant_id)->first();
          if ($variantInWh) {
            $warehouse->productVariants()->updateExistingPivot($cashierSalesReturn->product_variant_id, [
              'amount' => $variantInWh->pivot->amount + $quantityDifference,
            ]);
          }
        }
      }
    }
  }

  /**
   * Handle the CashierSalesReturn "deleted" event.
   */
  public function deleted(CashierSalesReturn $cashierSalesReturn): void
  {

    $fatora = $cashierSalesReturn->fatora;
    if ($fatora) {
      $fatora->decrement('full_price', $cashierSalesReturn->full_price);
    }



    $cashier = $cashierSalesReturn->cashier;
    if ($cashier) {
      $cashier->increment('daily_limit', $cashierSalesReturn->full_price);
    }

    $salesPoint = $cashier?->salesPoint;
    if ($salesPoint && $salesPoint->warehouse) {
      $warehouse = $salesPoint->warehouse;
      $variantInWarehouse = $warehouse->productVariants()
        ->where('product_variant_id', $cashierSalesReturn->product_variant_id)
        ->first();

      if ($variantInWarehouse) {
        $warehouse->productVariants()->updateExistingPivot($cashierSalesReturn->product_variant_id, [
          'amount' => $variantInWarehouse->pivot->amount - $cashierSalesReturn->quantity,
        ]);
      }
    }

    if ($fatora && $fatora->returns()->count() === 0) {
      $fatora->delete();
    }
  }

  /**
   * Handle the CashierSalesReturn "restored" event.
   */
  public function restored(CashierSalesReturn $cashierSalesReturn): void
  {
    //
  }

  /**
   * Handle the CashierSalesReturn "force deleted" event.
   */
  public function forceDeleted(CashierSalesReturn $cashierSalesReturn): void
  {
    //
  }
}