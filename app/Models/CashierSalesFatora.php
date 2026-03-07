<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierSalesFatora extends Model
{
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
