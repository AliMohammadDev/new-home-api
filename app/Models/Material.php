<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
  use HasFactory;
  protected $fillable = ['material'];
  public function productVariants()
  {
    return $this->hasMany(ProductVariant::class);
  }
}