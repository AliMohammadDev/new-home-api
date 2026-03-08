<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyTreasure extends Model
{
  protected $fillable = ['name', 'money'];

  public function entries()
  {
    return $this->hasMany(CompanyEntry::class);
  }
}
