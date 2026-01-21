<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantPackage extends Model
{
  use HasFactory;

  protected $fillable = [
    'product_variant_id',
    'quantity',
    'price',
  ];

  public function variant()
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }
}