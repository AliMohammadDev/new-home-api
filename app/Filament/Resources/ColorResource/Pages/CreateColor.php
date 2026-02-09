<?php

namespace App\Filament\Resources\ColorResource\Pages;

use App\Filament\Resources\ColorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateColor extends CreateRecord
{
  protected static string $resource = ColorResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url($this->getResource()::getUrl('index')),
    ];
  }
}