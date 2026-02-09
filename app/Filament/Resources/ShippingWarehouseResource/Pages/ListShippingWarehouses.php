<?php

namespace App\Filament\Resources\ShippingWarehouseResource\Pages;

use App\Filament\Resources\ShippingWarehouseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingWarehouses extends ListRecords
{
  protected static string $resource = ShippingWarehouseResource::class;

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
