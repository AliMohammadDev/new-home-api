<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyEntry extends Model
{
  use SoftDeletes;
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
