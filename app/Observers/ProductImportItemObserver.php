<?php

namespace App\Observers;

use App\Models\ProductImportItem;

class ProductImportItemObserver
{
  /**
   * Handle the ProductImportItem "created" event.
   */
  public function created(ProductImportItem $productImportItem): void
  {
    $variant = $productImportItem->productVariant;
    if ($variant) {
      $variant->increment('stock_quantity', $productImportItem->quantity);
    }
  }

  /**
   * Handle the ProductImportItem "updated" event.
   */
  public function updated(ProductImportItem $productImportItem): void
  {
    $variant = $productImportItem->productVariant;
    if ($variant && $productImportItem->wasChanged('quantity')) {
      $oldQty = $productImportItem->getOriginal('quantity');
      $newQty = $productImportItem->quantity;

      $difference = $newQty - $oldQty;
      $variant->increment('stock_quantity', $difference);
    }
  }

  /**
   * Handle the ProductImportItem "deleted" event.
   */
  public function deleted(ProductImportItem $productImportItem): void
  {
    $variant = $productImportItem->productVariant;
    if ($variant) {
      $variant->decrement('stock_quantity', $productImportItem->quantity);
    }
  }

  /**
   * Handle the ProductImportItem "restored" event.
   */
  public function restored(ProductImportItem $productImportItem): void
  {
    //
  }

  /**
   * Handle the ProductImportItem "force deleted" event.
   */
  public function forceDeleted(ProductImportItem $productImportItem): void
  {
    //
  }
}