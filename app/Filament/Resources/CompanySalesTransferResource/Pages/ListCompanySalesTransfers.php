<?php

namespace App\Filament\Resources\CompanySalesTransferResource\Pages;

use App\Filament\Resources\CompanySalesTransferResource;
use App\Models\CompanySalesTransfer;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCompanySalesTransfers extends ListRecords
{
  protected static string $resource = CompanySalesTransferResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }



}
