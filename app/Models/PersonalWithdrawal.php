<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalWithdrawal extends Model
{
  use SoftDeletes;
  protected $fillable = ['user_name', 'reason', 'amount', 'expense_date'];

  public function entry()
  {
    return $this->hasOne(PersonalWithdrawalEntry::class);
  }

}