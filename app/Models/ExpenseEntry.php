<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseEntry extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'expense_id',
    'company_treasure_id',
    'user_id',
    'amount',
    'note'
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function expense()
  {
    return $this->belongsTo(Expense::class);
  }

  public function treasure()
  {
    return $this->belongsTo(CompanyTreasure::class, 'company_treasure_id');
  }
}
