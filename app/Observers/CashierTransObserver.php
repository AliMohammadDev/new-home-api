<?php

namespace App\Observers;

use App\Models\SalesPointCashierTrans;

class CashierTransObserver
{
  /**
   * Handle the SalesPointCashierTrans "created" event.
   */
  public function created(SalesPointCashierTrans $salesPointCashierTrans): void
  {
    $cashier = $salesPointCashierTrans->cashier;
    $salesPoint = $salesPointCashierTrans->salesPoint;

    if ($salesPointCashierTrans->trans_type === 'deposit') {

      $cashier?->increment('daily_limit', $salesPointCashierTrans->amount);
      $salesPoint?->decrement('amount', $salesPointCashierTrans->amount);

    } elseif ($salesPointCashierTrans->trans_type === 'withdraw') {
      $cashier?->decrement('daily_limit', $salesPointCashierTrans->amount);
      $salesPoint?->increment('amount', $salesPointCashierTrans->amount);
    }
  }



  /**
   * Handle the SalesPointCashierTrans "updated" event.
   */
  public function updated(SalesPointCashierTrans $salesPointCashierTrans): void
  {
    $cashier = $salesPointCashierTrans->cashier;
    $salesPoint = $salesPointCashierTrans->salesPoint;

    if (!$cashier || !$salesPoint)
      return;

    $oldAmount = $salesPointCashierTrans->getOriginal('amount');
    $oldType = $salesPointCashierTrans->getOriginal('trans_type');

    if ($oldType === 'deposit') {
      $cashier->decrement('daily_limit', $oldAmount);
      $salesPoint->increment('amount', $oldAmount);
    } else {
      $cashier->increment('daily_limit', $oldAmount);
      $salesPoint->decrement('amount', $oldAmount);
    }

    if ($salesPointCashierTrans->trans_type === 'deposit') {
      $cashier->increment('daily_limit', $salesPointCashierTrans->amount);
      $salesPoint->decrement('amount', $salesPointCashierTrans->amount);
    } else {
      $cashier->decrement('daily_limit', $salesPointCashierTrans->amount);
      $salesPoint->increment('amount', $salesPointCashierTrans->amount);
    }
  }

  /**
   * Handle the SalesPointCashierTrans "deleted" event.
   */
  public function deleted(SalesPointCashierTrans $salesPointCashierTrans): void
  {

  }

  /**
   * Handle the SalesPointCashierTrans "restored" event.
   */
  public function restored(SalesPointCashierTrans $salesPointCashierTrans): void
  {
    //
  }

  /**
   * Handle the SalesPointCashierTrans "force deleted" event.
   */
  public function forceDeleted(SalesPointCashierTrans $salesPointCashierTrans): void
  {
    $cashier = $salesPointCashierTrans->cashier;
    $salesPoint = $salesPointCashierTrans->salesPoint;

    if ($salesPointCashierTrans->trans_type === 'deposit') {
      $cashier?->decrement('daily_limit', $salesPointCashierTrans->amount);
      $salesPoint?->increment('amount', $salesPointCashierTrans->amount);
    } else {
      $cashier?->increment('daily_limit', $salesPointCashierTrans->amount);
      $salesPoint?->decrement('amount', $salesPointCashierTrans->amount);
    }
  }
}
