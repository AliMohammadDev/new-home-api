<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPoint extends Model
{
  protected $fillable = [
    'warehouse_id',
    'name',
    'location',
    'phone',
    'amount',
    'is_active',
  ];
  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function warehouse()
  {
    return $this->belongsTo(Warehouse::class);
  }

  public function managers()
  {
    return $this->belongsToMany(User::class, 'sales_point_managers')
      ->withPivot(['id', 'phone'])
      ->withTimestamps();
  }

  public function cashier()
  {
    return $this->hasMany(SalesPointCashier::class);
  }
}