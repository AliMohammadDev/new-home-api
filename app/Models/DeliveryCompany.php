<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryCompany extends Model
{
  protected $fillable = [
    'user_id',
    'name',
    'phone',
    'email',
    'address',
    'is_active'
  ];


  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
  public function orders(): HasMany
  {
    return $this->hasMany(Order::class, 'delivery_company_id');
  }
}