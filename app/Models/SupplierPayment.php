<?php

namespace App\Models;

use App\Traits\FilterByYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPayment extends Model
{
  use SoftDeletes;
  use FilterByYear;
  protected $fillable = [
    'product_import_item_id',
    'amount',
    'payment_date',
    'payment_method',
    'trans_type',
    'notes',
  ];

  public function productImportItem()
  {
    return $this->belongsTo(ProductImportItem::class);
  }
}
