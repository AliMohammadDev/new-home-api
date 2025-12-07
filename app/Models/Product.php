<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $fillable = ['name', 'body', 'category_id', 'image', 'image_public_id', 'price', 'discount'];

  public function getFinalPriceAttribute()
  {
    return $this->price - ($this->discount ?? 0);
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function colors()
  {
    return $this->belongsToMany(Color::class);
  }

  public function sizes()
  {
    return $this->belongsToMany(Size::class);
  }

  public function materials()
  {
    return $this->belongsToMany(Material::class);
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
