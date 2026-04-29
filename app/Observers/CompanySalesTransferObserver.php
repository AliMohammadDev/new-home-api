<?php

namespace App\Observers;

use App\Models\CompanySalesTransfer;
use App\Models\CompanySalesTransferEntry;
use App\Models\CompanyTreasure;

class CompanySalesTransferObserver
{
  protected function getMainTreasure()
  {
    return CompanyTreasure::where('name', 'like', '%الصندوق الرئيسي%')->first() ?? CompanyTreasure::first();
  }

  public function created(CompanySalesTransfer $transfer): void
  {
    $mainTreasure = $this->getMainTreasure();
    $salesPoint = $transfer->salesPoint;

    if ($mainTreasure && $salesPoint) {
      $salesPoint->increment('amount', $transfer->quantity);
      $mainTreasure->decrement('money', $transfer->quantity);

      CompanySalesTransferEntry::create([
        'company_sales_transfer_id' => $transfer->id,
        'company_treasure_id' => $mainTreasure->id,
        'user_id' => auth()->id() ?? 1,
        'amount' => $transfer->quantity,
        'note' => "تحويل صادر لنقطة: " . $salesPoint->name,
      ]);
    }
  }

  public function updated(CompanySalesTransfer $transfer): void
  {
    if ($transfer->wasChanged('quantity')) {
      $oldQty = $transfer->getOriginal('quantity');
      $newQty = $transfer->quantity;
      $diff = $newQty - $oldQty;

      $mainTreasure = $this->getMainTreasure();
      $salesPoint = $transfer->salesPoint;

      if ($mainTreasure && $salesPoint) {
        $salesPoint->increment('amount', $diff);
        $mainTreasure->decrement('money', $diff);

        $transfer->entry()?->update([
          'amount' => $newQty,
        ]);
      }
    }
  }

  public function deleted(CompanySalesTransfer $transfer): void
  {

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
    $mainTreasure = $this->getMainTreasure();
    $salesPoint = $companySalesTransfer->salesPoint;

    if ($mainTreasure && $salesPoint) {
      $salesPoint->decrement('amount', $companySalesTransfer->quantity);
      $mainTreasure->increment('money', $companySalesTransfer->quantity);

      $companySalesTransfer->entry()?->delete();
    }
  }
}