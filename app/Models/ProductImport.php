<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImport extends Model
{
  use HasFactory;

  protected $fillable = [
    'supplier_name',
    'supplier_phone',
    'address',
    'notes'
  ];

  public function productVariants()
  {
    return $this->belongsToMany(ProductVariant::class, 'product_import_items')
      ->withPivot(['quantity', 'price', 'shipping_price', 'discount', 'expected_arrival'])
      ->withTimestamps();
  }
}
