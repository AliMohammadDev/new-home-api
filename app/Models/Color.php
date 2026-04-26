<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
  use HasFactory;
  protected $fillable = ['color', 'hex_code'];
  protected $casts = [
    'color' => 'array',
  ];


  public function productVariants()
  {
    return $this->hasMany(ProductVariant::class);
  }

  public function getTranslatedColorAttribute(): string
  {
    return $this->color[app()->getLocale()]
      ?? $this->color['en']
      ?? '';
  }
}