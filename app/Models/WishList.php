<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
  protected $fillable = ['user_id', 'product_variant_id'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function productVariant()
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }

  public function getCreatedAtFormattedAttribute()
  {
    return $this->created_at->format('H:i d, M Y');
  }
}
