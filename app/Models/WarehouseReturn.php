<?php

namespace App\Models;

use App\Traits\FilterByYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseReturn extends Model
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

  protected $casts = [
    'expected_arrival' => 'datetime',
  ];
  public function productVariant()
  {
    return $this->belongsTo(ProductVariant::class);
  }

  public function warehouse()
  {
    return $this->belongsTo(Warehouse::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
