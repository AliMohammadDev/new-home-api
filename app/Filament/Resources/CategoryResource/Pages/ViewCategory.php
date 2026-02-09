<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewCategory extends ViewRecord
{
  protected static string $resource = CategoryResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url($this->getResource()::getUrl('index')),
      Actions\EditAction::make(),
    ];
  }
}