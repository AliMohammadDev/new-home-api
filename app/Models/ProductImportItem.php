<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImportItem extends Model
{
  protected $fillable = [
    'product_import_id',
    'product_variant_id',
    'quantity',
    'price',
    'shipping_price',
    'discount',
    'expected_arrival'
  ];

  protected $casts = [
    'price' => 'float',
    'shipping_price' => 'float',
    'discount' => 'float',
    'expected_arrival' => 'datetime',
  ];

  public function productImport()
  {
    return $this->belongsTo(ProductImport::class);
  }

  public function productVariant()
  {
    return $this->belongsTo(ProductVariant::class);
  }
}