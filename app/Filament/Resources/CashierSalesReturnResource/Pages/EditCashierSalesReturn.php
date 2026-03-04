<?php

namespace App\Filament\Resources\CashierSalesReturnResource\Pages;

use App\Filament\Resources\CashierSalesReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashierSalesReturn extends EditRecord
{
  protected static string $resource = CashierSalesReturnResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }
}
