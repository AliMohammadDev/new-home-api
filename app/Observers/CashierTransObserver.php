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

    if ($cashier) {
      if ($salesPointCashierTrans->trans_type === 'deposit') {
        $cashier->increment('daily_limit', $salesPointCashierTrans->amount);
      } elseif ($salesPointCashierTrans->trans_type === 'withdrawal') {
        $cashier->decrement('daily_limit', $salesPointCashierTrans->amount);
      }
    }
  }

  /**
   * Handle the SalesPointCashierTrans "updated" event.
   */
  public function updated(SalesPointCashierTrans $salesPointCashierTrans): void
  {
    $cashier = $salesPointCashierTrans->cashier;
    if (!$cashier)
      return;

    $oldAmount = $salesPointCashierTrans->getOriginal('amount');
    $newAmount = $salesPointCashierTrans->amount;
    $oldType = $salesPointCashierTrans->getOriginal('trans_type');
    $newType = $salesPointCashierTrans->trans_type;

    if ($oldType === 'deposit') {
      $cashier->decrement('daily_limit', $oldAmount);
    } else {
      $cashier->increment('daily_limit', $oldAmount);
    }

    if ($newType === 'deposit') {
      $cashier->increment('daily_limit', $newAmount);
    } else {
      $cashier->decrement('daily_limit', $newAmount);
    }
  }

  /**
   * Handle the SalesPointCashierTrans "deleted" event.
   */
  public function deleted(SalesPointCashierTrans $salesPointCashierTrans): void
  {
    $cashier = $salesPointCashierTrans->cashier;

    if ($cashier) {
      if ($salesPointCashierTrans->trans_type === 'deposit') {
        $cashier->decrement('daily_limit', $salesPointCashierTrans->amount);
      } elseif ($salesPointCashierTrans->trans_type === 'withdrawal') {
        $cashier->increment('daily_limit', $salesPointCashierTrans->amount);
      }
    }
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
    //
  }
}
