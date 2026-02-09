<?php

namespace App\Filament\Resources\WarehouseReturnResource\Pages;

use App\Filament\Resources\WarehouseReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWarehouseReturns extends ListRecords
{
  protected static string $resource = WarehouseReturnResource::class;

  protected function getHeaderActions(): array
  {
    return [
      \Filament\Actions\Action::make('back')
        ->label('رجوع ')
        ->url(url('/admin'))
        ->color('gray'),
      Actions\CreateAction::make(),
    ];
  }
}