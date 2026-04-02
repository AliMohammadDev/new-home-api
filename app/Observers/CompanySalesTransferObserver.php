<?php

namespace App\Observers;

use App\Models\CompanyEntry;
use App\Models\CompanySalesTransfer;
use App\Models\CompanyTreasure;
use App\Models\SalesPoint;

class CompanySalesTransferObserver
{
  /**
   * Handle the CompanySalesTransfer "created" event.
   */
  public function created(CompanySalesTransfer $companySalesTransfer): void
  {
    $salesPoint = $companySalesTransfer->salesPoint;
    $mainTreasure = CompanyTreasure::first();

    if ($companySalesTransfer->trans_type === 'deposit') {
      $salesPoint->increment('amount', $companySalesTransfer->quantity);

      if ($mainTreasure) {
        CompanyEntry::create([
          'company_treasure_id' => $mainTreasure->id,
          'user_id' => auth()->id() ?? 1,
          'trans_type' => 'withdraw',
          'amount' => $companySalesTransfer->quantity,
          'name' => "تحويل إلى نقطة بيع: " . $salesPoint->name,
        ]);
      }
    } else {
      $salesPoint->decrement('amount', $companySalesTransfer->quantity);

      if ($mainTreasure) {
        CompanyEntry::create([
          'company_treasure_id' => $mainTreasure->id,
          'user_id' => auth()->id() ?? 1,
          'trans_type' => 'deposit',
          'amount' => $companySalesTransfer->quantity,
          'name' => "توريد من نقطة بيع: " . $salesPoint->name,
        ]);
      }
    }
  }

  /**
   * Handle the CompanySalesTransfer "updated" event.
   */


  public function updated(CompanySalesTransfer $companySalesTransfer): void
  {
    if ($companySalesTransfer->isDirty(['quantity', 'trans_type', 'sales_point_id'])) {
      $mainTreasure = CompanyTreasure::first();

      $oldQuantity = $companySalesTransfer->getOriginal('quantity');
      $oldType = $companySalesTransfer->getOriginal('trans_type');
      $oldSalesPoint = SalesPoint::find($companySalesTransfer->getOriginal('sales_point_id'));

      if ($oldType === 'deposit') {
        $oldSalesPoint->decrement('amount', $oldQuantity);
        if ($mainTreasure) {
          CompanyEntry::create([
            'company_treasure_id' => $mainTreasure->id,
            'user_id' => auth()->id() ?? 1,
            'trans_type' => 'deposit',
            'amount' => $oldQuantity,
            'name' => "تصحيح تحويل ملغى لـ: " . $oldSalesPoint->name,
          ]);
        }
      } else {
        $oldSalesPoint->increment('amount', $oldQuantity);
        if ($mainTreasure) {
          CompanyEntry::create([
            'company_treasure_id' => $mainTreasure->id,
            'user_id' => auth()->id() ?? 1,
            'trans_type' => 'withdraw',
            'amount' => $oldQuantity,
            'name' => "تصحيح توريد ملغى من: " . $oldSalesPoint->name,
          ]);
        }
      }

      $newSalesPoint = $companySalesTransfer->salesPoint;
      if ($companySalesTransfer->trans_type === 'deposit') {
        $newSalesPoint->increment('amount', $companySalesTransfer->quantity);
        if ($mainTreasure) {
          CompanyEntry::create([
            'company_treasure_id' => $mainTreasure->id,
            'user_id' => auth()->id() ?? 1,
            'trans_type' => 'withdraw',
            'amount' => $companySalesTransfer->quantity,
            'name' => "تحويل معدل لـ: " . $newSalesPoint->name,
          ]);
        }
      } else {
        $newSalesPoint->decrement('amount', $companySalesTransfer->quantity);
        if ($mainTreasure) {
          CompanyEntry::create([
            'company_treasure_id' => $mainTreasure->id,
            'user_id' => auth()->id() ?? 1,
            'trans_type' => 'deposit',
            'amount' => $companySalesTransfer->quantity,
            'name' => "توريد معدل من: " . $newSalesPoint->name,
          ]);
        }
      }
    }
  }

  /**
   * Handle the CompanySalesTransfer "deleted" event.
   */

  public function deleted(CompanySalesTransfer $companySalesTransfer): void
  {
    $salesPoint = $companySalesTransfer->salesPoint;
    $mainTreasure = CompanyTreasure::first();

    if ($companySalesTransfer->trans_type === 'deposit') {
      $salesPoint->decrement('amount', $companySalesTransfer->quantity);
      if ($mainTreasure) {
        CompanyEntry::create([
          'company_treasure_id' => $mainTreasure->id,
          'user_id' => auth()->id() ?? 1,
          'trans_type' => 'deposit',
          'amount' => $companySalesTransfer->quantity,
          'name' => "إلغاء تحويل وحذف سجل لـ: " . $salesPoint->name,
        ]);
      }
    } else {
      $salesPoint->increment('amount', $companySalesTransfer->quantity);
      if ($mainTreasure) {
        CompanyEntry::create([
          'company_treasure_id' => $mainTreasure->id,
          'user_id' => auth()->id() ?? 1,
          'trans_type' => 'withdraw',
          'amount' => $companySalesTransfer->quantity,
          'name' => "إلغاء توريد وحذف سجل من: " . $salesPoint->name,
        ]);
      }
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
