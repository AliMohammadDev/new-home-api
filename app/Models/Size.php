<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
  protected $fillable = ['size'];
  public function products()
  {
    return $this->belongsToMany(Product::class);
  }

}
