<?php

namespace App\Models;

use App\MediaLibrary\ProductPathGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

class Product extends Model implements HasMedia
{
  use HasFactory;
  use InteractsWithMedia;
  protected $fillable = ['name', 'body', 'category_id', 'price', 'discount', 'is_featured'];


  public function getFinalPriceAttribute()
  {

    $discountedAmount = $this->price * ($this->discount / 100);
    return round($this->price - $discountedAmount, 2);
  }


  public function category()
  {
    return $this->belongsTo(Category::class);
  }
  public function variants()
  {
    return $this->hasMany(ProductVariant::class);
  }

  protected static function booting(): void
  {
    PathGeneratorFactory::setCustomPathGenerators(
      static::class,
      ProductPathGenerator::class
    );
  }
  public function registerMediaConversions(?Media $media = null): void
  {
    $this->addMediaConversion('default')
      ->fit(Fit::Max, 1000, 1000)
      ->quality(70)
      ->format('webp')
      ->nonQueued();
  }


}