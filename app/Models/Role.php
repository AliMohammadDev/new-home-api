<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
  protected $casts = [
    'display_name' => 'array',
  ];

  public function getTranslatedDisplayNameAttribute(): string
  {
    return $this->display_name[app()->getLocale()] ?? $this->display_name['en'] ?? '';
  }
}