<?php

namespace App\Filament\Resources\ShippingCityResource\Pages;

use App\Filament\Resources\ShippingCityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShippingCity extends CreateRecord
{
  protected static string $resource = ShippingCityResource::class;

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