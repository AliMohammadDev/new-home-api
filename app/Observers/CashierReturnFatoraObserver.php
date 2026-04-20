<?php

namespace App\Observers;

use App\Models\CashierReturnFatora;

class CashierReturnFatoraObserver
{

  public function deleting(CashierReturnFatora $fatora): void
  {
    foreach ($fatora->returns as $returnItem) {
      $returnItem->delete();
    }
  }
}