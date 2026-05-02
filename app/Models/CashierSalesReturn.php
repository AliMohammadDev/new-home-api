<?php

namespace App\Models;

use App\Traits\FilterByYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashierSalesReturn extends Model
{
  use SoftDeletes;
  use FilterByYear;

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
