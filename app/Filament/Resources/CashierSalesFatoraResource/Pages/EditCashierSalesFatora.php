<?php

namespace App\Filament\Resources\CashierSalesFatoraResource\Pages;

use App\Filament\Resources\CashierSalesFatoraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashierSalesFatora extends EditRecord
{
  protected static string $resource = CashierSalesFatoraResource::class;

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
