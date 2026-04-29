<?php

namespace App\Observers;

use App\Models\CompanyTreasure;
use App\Models\Expense;
use App\Models\ExpenseEntry;

class ExpenseObserver
{

  protected function getMainTreasure()
  {
    return CompanyTreasure::where('name', 'like', '%الصندوق الرئيسي%')->first() ?? CompanyTreasure::first();
  }

  public function created(Expense $expense): void
  {
    $mainTreasure = $this->getMainTreasure();
    if ($mainTreasure) {
      ExpenseEntry::create([
        'expense_id' => $expense->id,
        'company_treasure_id' => $mainTreasure->id,
        'user_id' => auth()->id() ?? 1,
        'amount' => $expense->amount,
        'note' => "مصروف عام: " . $expense->reason,
      ]);

      $mainTreasure->decrement('money', $expense->amount);
    }
  }

  public function updated(Expense $expense): void
  {
    if ($expense->wasChanged('amount')) {
      $oldAmount = $expense->getOriginal('amount');
      $newAmount = $expense->amount;
      $diff = $newAmount - $oldAmount;

      $treasure = $this->getMainTreasure();
      if ($treasure) {
        $treasure->decrement('money', $diff);

        $expense->entry()->update([
          'amount' => $newAmount,
          'note' => "تعديل مصروف: " . $expense->reason
        ]);
      }
    }
  }

  public function deleted(Expense $expense): void
  {

  }

  /**
   * Handle the Expense "restored" event.
   */
  public function restored(Expense $expense): void
  {
    //
  }

  /**
   * Handle the Expense "force deleted" event.
   */
  public function forceDeleted(Expense $expense): void
  {
    $treasure = $this->getMainTreasure();
    if ($treasure) {
      $treasure->increment('money', $expense->amount);

      $expense->entry()->delete();
    }
  }
}