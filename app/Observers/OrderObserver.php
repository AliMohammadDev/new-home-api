<?php

namespace App\Observers;

use App\Models\CompanyTreasure;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
  /**
   * Handle the Order "created" event.
   */
  public function created(Order $order): void
  {
    //
  }

  /**
   * Handle the Order "updated" event.
   */

  public function updated(Order $order): void
  {
    if ($order->isDirty('status')) {
      $newStatus = $order->status;
      $oldStatus = $order->getOriginal('status');

      $netAmount = $order->orderItems->sum('total');
      $treasure = CompanyTreasure::where('name', 'صندوق مبيعات المتجر الالكتروني')->first();

      DB::transaction(function () use ($order, $newStatus, $oldStatus, $netAmount, $treasure) {


        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
          foreach ($order->orderItems as $item) {
            if ($item->productVariant) {
              $item->productVariant->decrement('stock_quantity', $item->quantity);
            }
          }
          if ($treasure && $netAmount > 0) {
            $treasure->increment('money', $netAmount);
          }
        }

        if ($oldStatus === 'completed' && $newStatus !== 'completed') {
          foreach ($order->orderItems as $item) {
            if ($item->productVariant) {
              $item->productVariant->increment('stock_quantity', $item->quantity);
            }
          }
          if ($treasure && $netAmount > 0) {
            $treasure->decrement('money', $netAmount);
          }
        }

      });
    }
  }

  /**
   * Handle the Order "deleted" event.
   */
  public function deleted(Order $order): void
  {
    //
  }

  /**
   * Handle the Order "restored" event.
   */
  public function restored(Order $order): void
  {
    //
  }

  /**
   * Handle the Order "force deleted" event.
   */
  public function forceDeleted(Order $order): void
  {
    //
  }
}
