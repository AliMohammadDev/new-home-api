<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
  use SoftDeletes;

  protected $fillable = ['user_id', 'reason', 'amount', 'expense_date'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function entry()
  {
    return $this->hasOne(ExpenseEntry::class);
  }
}