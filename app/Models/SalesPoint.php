<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPoint extends Model
{
  protected $fillable = [
    'name',
    'location',
    'phone',
    'is_active',
  ];
  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function managers()
  {
    return $this->belongsToMany(User::class, 'sales_point_managers');
  }
}