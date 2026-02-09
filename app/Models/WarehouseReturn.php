<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseReturn extends Model
{
  protected $fillable = ['product_variant_id', 'warehouse_id', 'amount', 'reason'];

  public function productVariant()
  {
    return $this->belongsTo(ProductVariant::class);
  }

  public function warehouse()
  {
    return $this->belongsTo(Warehouse::class);
  }
}