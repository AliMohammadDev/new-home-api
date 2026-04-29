<?php

namespace App\Observers;

use App\Models\SalesPointCashierTrans;

class CashierTransObserver
{
  /**
   * Handle the SalesPointCashierTrans "created" event.
   */
  public function created(SalesPointCashierTrans $trans): void
  {
    $cashier = $trans->cashier;
    $salesPoint = $trans->salesPoint;

    if (!$cashier || !$salesPoint)
      return;

    if ($trans->trans_type === 'deposit') {
      $cashier->increment('daily_limit', $trans->amount);
      $salesPoint->decrement('amount', $trans->amount);
    } else {
      $cashier->decrement('daily_limit', $trans->amount);
      $salesPoint->increment('amount', $trans->amount);
    }
  }

  public function updated(SalesPointCashierTrans $trans): void
  {
    if (!$trans->isDirty(['amount', 'trans_type'])) {
      return;
    }

    $cashier = $trans->cashier;
    $salesPoint = $trans->salesPoint;

    if (!$cashier || !$salesPoint)
      return;

    $oldAmount = $trans->getOriginal('amount');
    $oldType = $trans->getOriginal('trans_type');

    if ($oldType === 'deposit') {
      $cashier->decrement('daily_limit', $oldAmount);
      $salesPoint->increment('amount', $oldAmount);
    } else {
      $cashier->increment('daily_limit', $oldAmount);
      $salesPoint->decrement('amount', $oldAmount);
    }
    if ($trans->trans_type === 'deposit') {
      $cashier->increment('daily_limit', $trans->amount);
      $salesPoint->decrement('amount', $trans->amount);
    } else {
      $cashier->decrement('daily_limit', $trans->amount);
      $salesPoint->increment('amount', $trans->amount);
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
  public function forceDeleted(SalesPointCashierTrans $trans): void
  {
    $cashier = $trans->cashier;
    $salesPoint = $trans->salesPoint;

    if ($cashier && $salesPoint) {
      if ($trans->trans_type === 'deposit') {
        $cashier->decrement('daily_limit', $trans->amount);
        $salesPoint->increment('amount', $trans->amount);
      } else {
        $cashier->increment('daily_limit', $trans->amount);
        $salesPoint->decrement('amount', $trans->amount);
      }
    }
  }
}