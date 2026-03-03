<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPointCashierTrans extends Model
{
  protected $fillable = [
    'sales_point_id',
    'sales_point_manager_id',
    'sales_point_cashier_id',
    'trans_type',
    'name',
    'date',
    'amount',
    'note'
  ];

  public function salesPoint()
  {
    return $this->belongsTo(SalesPoint::class);
  }

  public function manager()
  {
    return $this->belongsTo(SalesPointManager::class, 'sales_point_manager_id');
  }

  public function cashier()
  {
    return $this->belongsTo(SalesPointCashier::class, 'sales_point_cashier_id');
  }
}