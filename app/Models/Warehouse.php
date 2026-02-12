<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
  use HasFactory;
  protected $fillable = ['name', 'address', 'city', 'phone'];

  public function productVariants()
  {
    return $this->belongsToMany(ProductVariant::class, 'shipping_warehouses')
      ->withPivot('arrival_time', 'amount')
      ->withTimestamps();
  }
}