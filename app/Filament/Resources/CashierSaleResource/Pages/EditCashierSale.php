<?php

namespace App\Filament\Resources\CashierSaleResource\Pages;

use App\Filament\Resources\CashierSaleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashierSale extends EditRecord
{
  protected static string $resource = CashierSaleResource::class;

  protected function getHeaderActions(): array
  {
    return [
    ];
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
