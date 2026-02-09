<?php
namespace App\Filament\Resources\WarehouseReturnResource\Pages;
use App\Filament\Resources\WarehouseReturnResource;
use App\Models\ProductVariant;
use App\Models\ShippingWarehouse;
use Filament\Resources\Pages\CreateRecord;
use
Illuminate\Support\Facades\DB;
class CreateWarehouseReturn extends CreateRecord
{
  protected static string
  $resource = WarehouseReturnResource::class;
  public function mount(): void
  {
    parent::mount();
    $this->form->fill([
      'warehouse_id' => request('warehouse_id'),
      'product_variant_id' => request('product_variant_id'),
      'amount' => request('amount'),
      'reason' => request('reason')
    ]);
  }

  protected function afterCreate(): void
  {
    $data = $this->record;

    DB::transaction(function () use ($data) {

      $variant = ProductVariant::find($data->product_variant_id);
      if ($variant) {
        $variant->increment('stock_quantity', $data->amount);
      }

      ShippingWarehouse::where('warehouse_id', $data->warehouse_id)
        ->where('product_variant_id', $data->product_variant_id)
        ->where('amount', $data->amount)
        ->first()
          ?->delete();
    });
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}