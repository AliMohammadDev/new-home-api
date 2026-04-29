<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySalesTransfer extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'sales_point_id',
    'name',
    'date',
    'quantity',
    'note',
  ];

  public function salesPoint()
  {
    return $this->belongsTo(SalesPoint::class);
  }
  public function entry()
  {
    return $this->hasOne(CompanySalesTransferEntry::class, 'company_sales_transfer_id');
  }
}