<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashierSalesFatora extends Model
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

  public function items()
  {
    return $this->hasMany(CashierSale::class, 'cashier_sales_fatora_id');
  }
}