<?php

namespace App\Filament\Resources\ProductImportItemResource\Pages;

use App\Filament\Resources\ProductImportItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductImportItem extends CreateRecord
{
  protected static string $resource = ProductImportItemResource::class;

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
