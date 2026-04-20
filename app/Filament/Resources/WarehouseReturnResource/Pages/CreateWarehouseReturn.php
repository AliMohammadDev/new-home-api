<?php
namespace App\Filament\Resources\WarehouseReturnResource\Pages;
use App\Filament\Resources\WarehouseReturnResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\WarehouseReturn;
use Illuminate\Database\Eloquent\Model;
use
Illuminate\Support\Facades\DB;
class CreateWarehouseReturn extends CreateRecord
{
  protected static string
  $resource = WarehouseReturnResource::class;


  protected function handleRecordCreation(array $data): Model
  {
    $items = $data['shipping_items'] ?? [];
    $lastRecord = null;

    foreach ($items as $item) {
      $item['user_id'] = auth()->id();

      $lastRecord = WarehouseReturn::create($item);
    }

    return $lastRecord;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
