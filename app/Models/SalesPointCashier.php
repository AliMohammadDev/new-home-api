<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPointCashier extends Model
{
  protected $fillable = ['sales_point_id', 'user_id', 'shift_type', 'daily_limit'];

  public function salesPoint()
  {
    return $this->belongsTo(SalesPoint::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
