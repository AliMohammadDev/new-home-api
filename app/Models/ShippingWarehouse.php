<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingWarehouse extends Model
{
  protected $fillable = [
    'product_variant_id',
    'warehouse_id',
    'arrival_time',
    'amount',
    'unit_name',
    'unit_capacity'
  ];
  protected $table = 'shipping_warehouses';

  protected $casts = [
    'expected_arrival' => 'datetime',
  ];


  public function warehouse(): BelongsTo
  {
    return $this->belongsTo(Warehouse::class);
  }

  public function productVariant(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class);
  }
}
