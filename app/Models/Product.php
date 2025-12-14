<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;
  protected $fillable = ['name', 'body', 'category_id', 'image', 'image_public_id', 'price', 'discount', 'is_featured'];

  public function getFinalPriceAttribute()
  {
    return $this->price - ($this->discount ?? 0);
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }
  public function variants()
  {
    return $this->hasMany(ProductVariant::class);
  }

  public function reviews()
  {
    return $this->hasMany(Reviews::class);
  }

  public function averageRating()
  {
    return $this->reviews()->avg('rating');
  }

}