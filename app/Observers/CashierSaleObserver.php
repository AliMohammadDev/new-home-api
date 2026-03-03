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
  }

  /**
   * Handle the CashierSale "updated" event.
   */
  public function updated(CashierSale $cashierSale): void
  {
    //
  }

  /**
   * Handle the CashierSale "deleted" event.
   */
  public function deleted(CashierSale $cashierSale): void
  {
    $cashierSale->fatora->decrement('full_price', $cashierSale->full_price);
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