<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $fillable = [
    'user_id',
    'cart_id',
    'checkout_id',
    'total_amount',
    'delivery_company_id',
    'shipping_fee',
    'discount_fee',
    'payment_method',
    'status'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function cart()
  {
    return $this->belongsTo(Cart::class);
  }

  public function checkout()
  {
    return $this->belongsTo(Checkout::class);
  }

  public function orderItems()
  {
    return $this->hasMany(OrderItem::class);
  }

  public function getCreatedAtFormattedAttribute()
  {
    return $this->created_at->format('H:i d, M Y');
  }

  public function deliveryCompany()
  {
    return $this->belongsTo(DeliveryCompany::class);
  }
}
