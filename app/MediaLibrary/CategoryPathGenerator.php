<?php

namespace App\MediaLibrary;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CategoryPathGenerator implements PathGenerator
{
  public function getPath(Media $media): string
  {
    return 'categories/' . $media->id . '/';
  }

  public function getPathForConversions(Media $media): string
  {
    return 'categories/' . $media->id . '/conversions/';
  }

  public function getPathForResponsiveImages(Media $media): string
  {
    return 'categories/' . $media->id . '/responsive/';
  }
}