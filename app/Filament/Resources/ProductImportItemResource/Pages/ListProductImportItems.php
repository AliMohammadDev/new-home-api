<?php

namespace App\Filament\Resources\ProductImportItemResource\Pages;

use App\Filament\Resources\ProductImportItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductImportItems extends ListRecords
{
  protected static string $resource = ProductImportItemResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url($this->getResource()::getUrl('index')),
      Actions\CreateAction::make(),
    ];
  }
}
