<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashierSale extends Model
{

  use SoftDeletes;
  protected $fillable = [
    'cashier_sales_fatora_id',
    'product_variant_id',
    'sales_point_cashier_id',
    'quantity',
    'price',
    'full_price'
  ];

  public function fatora()
  {
    return $this->belongsTo(CashierSalesFatora::class, 'cashier_sales_fatora_id');
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
