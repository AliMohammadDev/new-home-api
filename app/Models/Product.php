<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $fillable = ['name', 'body', 'category_id', 'image', 'price', 'discount'];
}
