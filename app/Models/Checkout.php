<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
  protected $fillable = [
    'first_name',
    'last_name',
    'city',
    'address',
    'cart_id',
    'user_id',
    'status',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function cart()
  {
    return $this->belongsTo(Cart::class);
  }

  public function order()
  {
    return $this->hasOne(Order::class);
  }
}
