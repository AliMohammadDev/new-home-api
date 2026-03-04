<?php

namespace App\Observers;

use App\Models\CompanySalesTransfer;
use App\Models\SalesPoint;

class CompanySalesTransferObserver
{
  /**
   * Handle the CompanySalesTransfer "created" event.
   */
  public function created(CompanySalesTransfer $companySalesTransfer): void
  {
    $salesPoint = $companySalesTransfer->salesPoint;

    if ($companySalesTransfer->trans_type === 'deposit') {
      $salesPoint->increment('amount', $companySalesTransfer->quantity);
    } else {
      $salesPoint->decrement('amount', $companySalesTransfer->quantity);
    }
  }

  /**
   * Handle the CompanySalesTransfer "updated" event.
   */
  public function updated(CompanySalesTransfer $companySalesTransfer): void
  {
    if ($companySalesTransfer->isDirty(['quantity', 'trans_type', 'sales_point_id'])) {

      $oldQuantity = $companySalesTransfer->getOriginal('quantity');
      $oldType = $companySalesTransfer->getOriginal('trans_type');
      $oldSalesPoint = SalesPoint::find($companySalesTransfer->getOriginal('sales_point_id'));

      if ($oldType === 'deposit') {
        $oldSalesPoint->decrement('amount', $oldQuantity);
      } else {
        $oldSalesPoint->increment('amount', $oldQuantity);
      }

      $newSalesPoint = $companySalesTransfer->salesPoint;
      if ($companySalesTransfer->trans_type === 'deposit') {
        $newSalesPoint->increment('amount', $companySalesTransfer->quantity);
      } else {
        $newSalesPoint->decrement('amount', $companySalesTransfer->quantity);
      }
    }
  }

  /**
   * Handle the CompanySalesTransfer "deleted" event.
   */
  public function deleted(CompanySalesTransfer $companySalesTransfer): void
  {
    $salesPoint = $companySalesTransfer->salesPoint;

    if ($companySalesTransfer->trans_type === 'deposit') {
      $salesPoint->decrement('amount', $companySalesTransfer->quantity);
    } else {
      $salesPoint->increment('amount', $companySalesTransfer->quantity);
    }
  }

  /**
   * Handle the CompanySalesTransfer "restored" event.
   */
  public function restored(CompanySalesTransfer $companySalesTransfer): void
  {
    //
  }

  /**
   * Handle the CompanySalesTransfer "force deleted" event.
   */
  public function forceDeleted(CompanySalesTransfer $companySalesTransfer): void
  {
    //
  }
}