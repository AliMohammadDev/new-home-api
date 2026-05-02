<?php

namespace App\Models;

use App\Traits\FilterByYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalWithdrawalEntry extends Model
{
  use SoftDeletes;
  use FilterByYear;

  protected $fillable = [
    'personal_withdrawal_id',
    'company_treasure_id',
    'user_id',
    'amount',
    'note'
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function withdrawal()
  {
    return $this->belongsTo(PersonalWithdrawal::class, 'personal_withdrawal_id');
  }

  public function treasure()
  {
    return $this->belongsTo(CompanyTreasure::class, 'company_treasure_id');
  }
}
