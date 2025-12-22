<?php

namespace App\MediaLibrary;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductPathGenerator implements PathGenerator
{
  public function getPath(Media $media): string
  {
    return 'products/' . $media->id . '/';
  }

  public function getPathForConversions(Media $media): string
  {
    return 'products/' . $media->id . '/conversions/';
  }

  public function getPathForResponsiveImages(Media $media): string
  {
    return 'products/' . $media->id . '/responsive/';
  }
}
