<?php

namespace App\Filament\Resources\CompanySalesTransferResource\Pages;

use App\Filament\Resources\CompanySalesTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanySalesTransfer extends EditRecord
{
  protected static string $resource = CompanySalesTransferResource::class;

  protected function getHeaderActions(): array
  {
    return [
    ];
  }
}
