<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPointManager extends Model
{
  protected $fillable = [
    'sales_point_id',
    'user_id',
    'phone'
  ];

  public function salesPoint()
  {
    return $this->belongsTo(SalesPoint::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
