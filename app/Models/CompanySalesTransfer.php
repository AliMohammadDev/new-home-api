<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySalesTransfer extends Model
{
  protected $fillable = [
    'sales_point_id',
    'trans_type',
    'name',
    'date',
    'quantity',
    'note',
  ];

  public function salesPoint()
  {
    return $this->belongsTo(SalesPoint::class);
  }
}
