<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterByYear
{

  public function scopeForActiveYear(Builder $query)
  {
    $year = session('active_financial_year', date('Y'));

    return $query->whereYear('created_at', $year);
  }
}
