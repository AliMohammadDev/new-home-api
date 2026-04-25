<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
