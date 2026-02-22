<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
  use HasFactory, SoftDeletes;
  protected $fillable = [
    'product_id',
    'color_id',
    'size_id',
    'material_id',
    'price',
    'discount',
    'stock_quantity',
    'sku',
    'barcode'
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($variant) {
      if (empty($variant->sku)) {
        $variant->sku = self::generateUniqueSku();
      }
      if (empty($variant->barcode)) {
        $variant->barcode = self::generateUniqueBarcode();
      }
    });
  }

  public static function generateUniqueBarcode()
  {
    do {
      $barcode = mt_rand(100000000000, 999999999999);
    } while (self::where('barcode', $barcode)->exists());

    return $barcode;
  }

  public static function generateUniqueSku()
  {
    do {
      $sku = 'PROD-' . strtoupper(Str::random(8));
    } while (self::where('sku', $sku)->exists());

    return $sku;
  }

  public function getFinalPriceAttribute()
  {
    $discountedAmount = $this->price * ($this->discount / 100);
    return round($this->price - $discountedAmount, 2);
  }
  public function images()
  {
    return $this->hasMany(ProductVariantImage::class, 'product_variant_id');
  }
  /*
  |--------------------------------------------------------------------------
  | Relationships
  |--------------------------------------------------------------------------
  */


  public function packages()
  {
    return $this->hasMany(ProductVariantPackage::class, 'product_variant_id');
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function color()
  {
    return $this->belongsTo(Color::class);
  }

  public function size()
  {
    return $this->belongsTo(Size::class);
  }

  public function material()
  {
    return $this->belongsTo(Material::class);
  }

  public function wishlists()
  {
    return $this->hasMany(WishList::class);
  }

  public function cartItems()
  {
    return $this->hasMany(CartItem::class);
  }

  public function orderItems()
  {
    return $this->hasMany(OrderItem::class);
  }

  public function reviews()
  {
    return $this->hasMany(Reviews::class);
  }

  public function imports()
  {
    return $this->belongsToMany(ProductImport::class, 'product_import_items')
      ->withPivot(['quantity', 'price', 'shipping_price', 'discount', 'expected_arrival'])
      ->withTimestamps();
  }

  public function warehouses()
  {
    return $this->belongsToMany(Warehouse::class, 'shipping_warehouses')
      ->withPivot('arrival_time', 'amount')
      ->withTimestamps();
  }

}