<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashierReturnFatora extends Model
{

  use SoftDeletes;
  protected $fillable = [
    'sales_point_cashier_id',
    'date',
    'full_price'
  ];

  public function cashier()
  {
    return $this->belongsTo(SalesPointCashier::class, 'sales_point_cashier_id');
  }

  public function returns()
  {
    return $this->hasMany(CashierSalesReturn::class, 'cashier_return_fatora_id');
  }
}
