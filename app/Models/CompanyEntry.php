<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyEntry extends Model
{
  protected $fillable = ['company_treasure_id', 'user_id', 'trans_type', 'name', 'amount'];

  public function treasure()
  {
    return $this->belongsTo(CompanyTreasure::class, 'company_treasure_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
