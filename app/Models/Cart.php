<?php

namespace App\Models;

use App\Traits\FilterByYear;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
  use FilterByYear;

  protected $fillable = [
    'user_id',
    'status',
  ];
  public function cartItems()
  {
    return $this->hasMany(CartItem::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
