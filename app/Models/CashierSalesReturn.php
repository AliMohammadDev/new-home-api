<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierSalesReturn extends Model
{
  protected $fillable = [
    'cashier_return_fatora_id',
    'product_variant_id',
    'sales_point_cashier_id',
    'quantity',
    'price',
    'full_price'
  ];

  public function fatora()
  {
    return $this->belongsTo(CashierReturnFatora::class, 'cashier_return_fatora_id');
  }

  public function variant()
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }

  public function cashier()
  {
    return $this->belongsTo(SalesPointCashier::class, 'sales_point_cashier_id');
  }
}