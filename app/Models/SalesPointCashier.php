<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPointCashier extends Model
{
  protected $fillable = ['sales_point_id', 'user_id', 'shift_type', 'daily_limit'];

  public function getCashierNameAttribute()
  {
    return $this->user?->name;
  }


  public function salesPoint()
  {
    return $this->belongsTo(SalesPoint::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function fatora()
  {
    return $this->hasMany(CashierSalesFatora::class, 'sales_point_cashier_id');
  }

  public function transactions()
  {
    return $this->hasMany(SalesPointCashierTrans::class, 'sales_point_cashier_id');
  }
}