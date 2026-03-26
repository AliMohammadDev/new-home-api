<?php

namespace App\Observers;

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
    if ($order->isDirty('status') && $order->status === 'cancelled' && $order->getOriginal('status') !== 'cancelled') {

      DB::transaction(function () use ($order) {
        foreach ($order->orderItems as $item) {
          if ($item->productVariant) {
            $item->productVariant->increment('stock_quantity', $item->quantity);
          }
        }
      });
    }

    if ($order->isDirty('status') && $order->getOriginal('status') === 'cancelled' && $order->status !== 'cancelled') {
      DB::transaction(function () use ($order) {
        foreach ($order->orderItems as $item) {
          if ($item->productVariant) {
            $item->productVariant->decrement('stock_quantity', $item->quantity);
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
