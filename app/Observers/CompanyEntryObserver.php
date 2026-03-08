<?php

namespace App\Observers;

use App\Models\CompanyEntry;

class CompanyEntryObserver
{
  /**
   * Handle the CompanyEntry "created" event.
   */
  public function created(CompanyEntry $companyEntry): void
  {
    $treasure = $companyEntry->treasure;
    if ($companyEntry->trans_type === 'deposit') {
      $treasure->increment('money', $companyEntry->amount);
    } else {
      $treasure->decrement('money', $companyEntry->amount);
    }
  }
  /**
   * Handle the CompanyEntry "updated" event.
   */
  public function updated(CompanyEntry $companyEntry): void
  {
    if ($companyEntry->wasChanged(['amount', 'trans_type', 'company_treasure_id'])) {
      $oldAmount = $companyEntry->getOriginal('amount');
      $oldType = $companyEntry->getOriginal('trans_type');
      $treasure = $companyEntry->treasure;

      if ($oldType === 'deposit') {
        $treasure->decrement('money', $oldAmount);
      } else {
        $treasure->increment('money', $oldAmount);
      }

      if ($companyEntry->trans_type === 'deposit') {
        $treasure->increment('money', $companyEntry->amount);
      } else {
        $treasure->decrement('money', $companyEntry->amount);
      }
    }
  }


  /**
   * Handle the CompanyEntry "deleted" event.
   */
  public function deleted(CompanyEntry $companyEntry): void
  {
    $treasure = $companyEntry->treasure;
    if ($companyEntry->trans_type === 'deposit') {
      $treasure->decrement('money', $companyEntry->amount);
    } else {
      $treasure->increment('money', $companyEntry->amount);
    }
  }
  /**
   * Handle the CompanyEntry "restored" event.
   */
  public function restored(CompanyEntry $companyEntry): void
  {
    //
  }

  /**
   * Handle the CompanyEntry "force deleted" event.
   */
  public function forceDeleted(CompanyEntry $companyEntry): void
  {
    //
  }
}