<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
  protected $fillable = [
    'product_id',
    'color_id',
    'size_id',
    'material_id',
    'stock_quantity',
  ];

  /*
  |--------------------------------------------------------------------------
  | Relationships
  |--------------------------------------------------------------------------
  */

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function color()
  {
    return $this->belongsTo(Color::class);
  }

  public function size()
  {
    return $this->belongsTo(Size::class);
  }

  public function material()
  {
    return $this->belongsTo(Material::class);
  }

  public function cartItems()
  {
    return $this->hasMany(CartItem::class);
  }

  public function orderItems()
  {
    return $this->hasMany(OrderItem::class);
  }
}
