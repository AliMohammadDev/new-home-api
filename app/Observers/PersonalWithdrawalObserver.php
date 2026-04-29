<?php

namespace App\Observers;

use App\Models\CompanyTreasure;
use App\Models\PersonalWithdrawal;
use App\Models\PersonalWithdrawalEntry;

class PersonalWithdrawalObserver
{

  protected function getMainTreasure()
  {
    return CompanyTreasure::where('name', 'like', '%الصندوق الرئيسي%')->first() ?? CompanyTreasure::first();
  }

  public function created(PersonalWithdrawal $personalWithdrawal): void
  {
    $mainTreasure = $this->getMainTreasure();
    if ($mainTreasure) {
      PersonalWithdrawalEntry::create([
        'personal_withdrawal_id' => $personalWithdrawal->id,
        'company_treasure_id' => $mainTreasure->id,
        'user_id' => auth()->id() ?? 1,
        'amount' => $personalWithdrawal->amount,
        'note' => "مسحوبات شخصية لـ: " . $personalWithdrawal->user_name . " - " . $personalWithdrawal->reason,
      ]);

      $mainTreasure->decrement('money', $personalWithdrawal->amount);
    }
  }

  public function updated(PersonalWithdrawal $personalWithdrawal): void
  {
    if ($personalWithdrawal->wasChanged('amount')) {
      $oldAmount = $personalWithdrawal->getOriginal('amount');
      $newAmount = $personalWithdrawal->amount;
      $diff = $newAmount - $oldAmount;

      $treasure = $this->getMainTreasure();
      if ($treasure) {
        $treasure->decrement('money', $diff);

        $personalWithdrawal->entry()->update([
          'amount' => $newAmount,
          'note' => "تعديل مسحوبات لـ: " . $personalWithdrawal->user_name
        ]);
      }
    }
  }

  public function deleted(PersonalWithdrawal $personalWithdrawal): void
  {

  }
  /**
   * Handle the PersonalWithdrawal "restored" event.
   */
  public function restored(PersonalWithdrawal $personalWithdrawal): void
  {
    //
  }

  /**
   * Handle the PersonalWithdrawal "force deleted" event.
   */
  public function forceDeleted(PersonalWithdrawal $personalWithdrawal): void
  {
    $treasure = $this->getMainTreasure();
    if ($treasure) {
      $treasure->increment('money', $personalWithdrawal->amount);

      $personalWithdrawal->entry()->delete();
    }
  }
}
