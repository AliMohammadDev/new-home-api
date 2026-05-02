<?php

namespace App\Models;

use App\Traits\FilterByYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingWarehouse extends Model
{
  use SoftDeletes;
  use FilterByYear;

  protected $fillable = [
    'product_variant_id',
    'user_id',
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

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function warehouse(): BelongsTo
  {
    return $this->belongsTo(Warehouse::class);
  }

  public function productVariant(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class);
  }
}