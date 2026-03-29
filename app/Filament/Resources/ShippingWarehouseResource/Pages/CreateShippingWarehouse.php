<?php

namespace App\Filament\Resources\ShippingWarehouseResource\Pages;

use App\Filament\Resources\ShippingWarehouseResource;
use App\Models\ShippingWarehouse;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShippingWarehouse extends CreateRecord
{
  protected static string $resource = ShippingWarehouseResource::class;

  protected function getHeaderActions(): array
  {
    return [
      \Filament\Actions\Action::make('back')
        ->label('رجوع ')
        ->url($this->getResource()::getUrl('index'))
        ->color('gray'),
    ];
  }


  protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
  {
    $items = $data['shipping_items'] ?? [];
    $lastRecord = null;

    foreach ($items as $item) {
      $item['user_id'] = auth()->id();
      $lastRecord = ShippingWarehouse::create($item);
    }

    return $lastRecord;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}