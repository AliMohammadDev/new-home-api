<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
  protected $fillable = [
    'cart_id',
    'product_variant_id',
    'product_variant_package_id',
    'quantity',
  ];

  public function cart()
  {
    return $this->belongsTo(Cart::class);
  }
  public function productVariant()
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }

  public function productVariantPackage()
  {
    return $this->belongsTo(ProductVariantPackage::class, 'product_variant_package_id');
  }

}