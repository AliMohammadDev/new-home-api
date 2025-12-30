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

  protected $casts = [
    'name' => 'array',
    'description' => 'array',
  ];


  public function getTranslatedNameAttribute(): string
  {
    return $this->name[app()->getLocale()]
      ?? $this->name['en']
      ?? '';
  }

  public function getTranslatedDescriptionAttribute(): string
  {
    return $this->description[app()->getLocale()]
      ?? $this->description['en']
      ?? '';
  }


}