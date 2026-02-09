<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImport extends Model
{
  use HasFactory;

  protected $fillable = [
    'product_variant_id',
    'quantity',
    'address',
    'supplier_name',
    'import_date',
    'notes'
  ];

  public function productVariants()
  {
    return $this->hasMany(ProductVariant::class);
  }
}