<?php

namespace App\Observers;

use App\Models\CompanyEntry;
use App\Models\CompanyTreasure;
use App\Models\ProductImportItem;

class ProductImportItemObserver
{
  /**
   * Handle the ProductImportItem "created" event.
   */
  // داخل ملف ProductImportItemObserver.php

  public function created(ProductImportItem $productImportItem): void
  {
    $variant = $productImportItem->productVariant;
    if ($variant) {
      $variant->increment('stock_quantity', $productImportItem->quantity);
    }

    $price = (float) ($productImportItem->price ?? 0);
    $shipping = (float) ($productImportItem->shipping_price ?? 0);
    $quantity = (float) ($productImportItem->quantity ?? 0);
    $discount = (float) ($productImportItem->discount ?? 0);

    $calculatedTotal = (($price + $shipping) * $quantity) - $discount;

    $mainTreasure = CompanyTreasure::first();
    if ($mainTreasure && $calculatedTotal > 0) {
      CompanyEntry::create([
        'company_treasure_id' => $mainTreasure->id,
        'user_id' => auth()->id() ?? 1,
        'trans_type' => 'withdrawal',
        'amount' => $calculatedTotal,
        'name' => "شراء بضاعة: " . ($variant?->product?->name['ar'] ?? 'صنف مستورد'),
      ]);
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

    if ($productImportItem->wasChanged('total_cost')) {
      $mainTreasure = CompanyTreasure::first();
      $oldTotal = $productImportItem->getOriginal('total_cost');
      $newTotal = $productImportItem->total_cost;

      if ($mainTreasure) {
        CompanyEntry::create([
          'company_treasure_id' => $mainTreasure->id,
          'user_id' => auth()->id() ?? 1,
          'trans_type' => 'deposit',
          'amount' => $oldTotal,
          'name' => "تصحيح تكلفة استيراد (مبلغ قديم)",
        ]);

        CompanyEntry::create([
          'company_treasure_id' => $mainTreasure->id,
          'user_id' => auth()->id() ?? 1,
          'trans_type' => 'withdrawal',
          'amount' => $newTotal,
          'name' => "تعديل تكلفة استيراد: " . ($variant?->product?->name['ar'] ?? 'صنف'),
        ]);
      }
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

    $mainTreasure = CompanyTreasure::first();
    if ($mainTreasure && $productImportItem->total_cost > 0) {
      CompanyEntry::create([
        'company_treasure_id' => $mainTreasure->id,
        'user_id' => auth()->id() ?? 1,
        'trans_type' => 'deposit',
        'amount' => $productImportItem->total_cost,
        'name' => "إلغاء عملية استيراد وإعادة المبلغ للخزينة",
      ]);
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
