<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
  protected $fillable = ['material'];

  public function products()
  {
    return $this->belongsToMany(Product::class);
  }
}
