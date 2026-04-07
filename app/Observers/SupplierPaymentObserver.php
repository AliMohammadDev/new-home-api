<?php

namespace App\Observers;

use App\Models\CompanyEntry;
use App\Models\CompanyTreasure;
use App\Models\SupplierPayment;

class SupplierPaymentObserver
{
  /**
   * Handle the SupplierPayment "created" event.
   */
  public function created(SupplierPayment $supplierPayment): void
  {
    $mainTreasure = CompanyTreasure::first();
    if ($mainTreasure && $supplierPayment->amount > 0) {
      CompanyEntry::create([
        'company_treasure_id' => $mainTreasure->id,
        'user_id' => auth()->id() ?? 1,
        'trans_type' => 'withdraw',
        'amount' => $supplierPayment->amount,
        'name' => "دفع لمورد: " . ($supplierPayment->productImportItem->productImport->supplier_name ?? 'مورد'),
      ]);
    }
  }

  /**
   * Handle the SupplierPayment "updated" event.
   */
  public function updated(SupplierPayment $supplierPayment): void
  {
    if ($supplierPayment->wasChanged('amount')) {
      $mainTreasure = CompanyTreasure::first();
      $oldAmount = $supplierPayment->getOriginal('amount');
      $newAmount = $supplierPayment->amount;
      // company entry

    }
  }

  /**
   * Handle the SupplierPayment "deleted" event.
   */
  public function deleted(SupplierPayment $supplierPayment): void
  {
    $mainTreasure = CompanyTreasure::first();
    if ($mainTreasure && $supplierPayment->amount > 0) {
      CompanyEntry::create([
        'company_treasure_id' => $mainTreasure->id,
        'user_id' => auth()->id() ?? 1,
        'trans_type' => 'deposit',
        'amount' => $supplierPayment->amount,
        'name' => "إلغاء دفعة مورد وإعادة المبلغ للخزينة",
      ]);
    }
  }

  /**
   * Handle the SupplierPayment "restored" event.
   */
  public function restored(SupplierPayment $supplierPayment): void
  {
    //
  }

  /**
   * Handle the SupplierPayment "force deleted" event.
   */
  public function forceDeleted(SupplierPayment $supplierPayment): void
  {
    //
  }
}
