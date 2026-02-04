<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCity extends Model
{
  protected $fillable = [
    'city_name',
    'estimated_delivery',
    'shipping_fee',
    'is_active',
    'is_free_shipping'
  ];

  protected $casts = [
    'city_name' => 'array',
  ];

  public function getTranslatedCityNameAttribute(): string
  {
    return $this->city_name[app()->getLocale()]
      ?? $this->city_name['en']
      ?? '';
  }

  public function checkouts()
  {
    return $this->hasMany(Checkout::class);
  }
}