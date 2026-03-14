<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImportItem extends Model
{
  protected $fillable = [
    'product_import_id',
    'product_variant_id',
    'user_id',
    'quantity',
    'price',
    'shipping_price',
    'discount',
    'total_cost',
    'expected_arrival'
  ];

  protected $casts = [
    'price' => 'float',
    'shipping_price' => 'float',
    'discount' => 'float',
    'expected_arrival' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function productImport()
  {
    return $this->belongsTo(ProductImport::class);
  }

  public function productVariant()
  {
    return $this->belongsTo(ProductVariant::class);
  }
}