<?php

namespace App\Filament\Resources\ShippingWarehouseResource\Pages;

use App\Filament\Resources\ShippingWarehouseResource;
use App\Models\ShippingWarehouse;
use Filament\Actions;
use Filament\Notifications\Notification;
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

  protected function afterCreate(): void
  {
    $record = $this->record;
    $stock = $record->productVariant?->stock_quantity;

    if ($stock < 0) {
      Notification::make()
        ->title('تنبيه: تم تجاوز المخزون')
        ->body("تمت العملية بنجاح، ولكن المخزون الحالي للمنتج أصبح بالسالب ($stock).")
        ->warning()
        ->persistent()
        ->send();
    }
  }
}
