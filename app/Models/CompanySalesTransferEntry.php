<?php

namespace App\Models;

use App\Traits\FilterByYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySalesTransferEntry extends Model
{
  use SoftDeletes;
  use FilterByYear;

  protected $fillable = [
    'company_sales_transfer_id',
    'company_treasure_id',
    'user_id',
    'amount',
    'note'
  ];

  public function transfer(): BelongsTo
  {
    return $this->belongsTo(CompanySalesTransfer::class, 'company_sales_transfer_id');
  }

  public function treasure(): BelongsTo
  {
    return $this->belongsTo(CompanyTreasure::class, 'company_treasure_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
