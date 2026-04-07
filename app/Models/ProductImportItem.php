<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImportItem extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'product_import_id',
    'product_variant_id',
    'user_id',
    'quantity',
    'price',
    'shipping_price',
    'discount',
    'total_cost',
    'expected_arrival'
  ];

  protected $casts = [
    'price' => 'float',
    'shipping_price' => 'float',
    'discount' => 'float',
    'expected_arrival' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function productImport()
  {
    return $this->belongsTo(ProductImport::class);
  }

  public function productVariant()
  {
    return $this->belongsTo(ProductVariant::class);
  }


  public function payments()
  {
    return $this->hasMany(SupplierPayment::class);
  }

  public function getTotalPaidAttribute(): float
  {
    return (float) $this->payments()->sum('amount');
  }

  public function getRemainingAmountAttribute(): float
  {
    return max(0, (float) $this->total_cost - $this->total_paid);
  }

}
