<?php

namespace App\Filament\Resources\WarehouseResource\Pages;

use App\Filament\Resources\WarehouseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWarehouse extends EditRecord
{
  protected static string $resource = WarehouseResource::class;

  protected function getHeaderActions(): array
  {

    return [
      \Filament\Actions\Action::make('back')
        ->label('رجوع ')
        ->url($this->getResource()::getUrl('index'))
        ->color('gray'),
      \Filament\Actions\CreateAction::make(),
      Actions\DeleteAction::make(),

    ];
  }
}