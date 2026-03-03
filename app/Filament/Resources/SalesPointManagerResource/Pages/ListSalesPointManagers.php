<?php

namespace App\Filament\Resources\SalesPointManagerResource\Pages;

use App\Filament\Resources\SalesPointManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalesPointManagers extends ListRecords
{
  protected static string $resource = SalesPointManagerResource::class;

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