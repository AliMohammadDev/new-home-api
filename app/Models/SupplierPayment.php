<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
  protected $fillable = [
    'product_import_item_id',
    'amount',
    'payment_date',
    'payment_method',
    'trans_type',
    'notes'
  ];

  public function productImportItem()
  {
    return $this->belongsTo(ProductImportItem::class);
  }


}