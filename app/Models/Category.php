<?php

namespace App\Models;

use App\MediaLibrary\CategoryPathGenerator;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

class Category extends Model implements HasMedia
{
  use InteractsWithMedia;

  protected $fillable = ['name', 'description'];

  protected static function booting(): void
  {
    PathGeneratorFactory::setCustomPathGenerators(
      static::class,
      CategoryPathGenerator::class
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

  public function products()
  {
    return $this->hasMany(Product::class);
  }

}