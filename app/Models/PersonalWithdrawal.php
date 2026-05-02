<?php

namespace App\Models;

use App\Traits\FilterByYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalWithdrawal extends Model
{
  use SoftDeletes;
  use FilterByYear;

  protected $fillable = ['user_name', 'reason', 'amount', 'expense_date'];

  public function entry()
  {
    return $this->hasOne(PersonalWithdrawalEntry::class);
  }
}
