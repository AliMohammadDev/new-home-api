<?php

namespace App\Filament\Resources\SalesPointCashierTransResource\Pages;

use App\Filament\Resources\SalesPointCashierTransResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalesPointCashierTrans extends ListRecords
{
  protected static string $resource = SalesPointCashierTransResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url('/admin'),
      Actions\CreateAction::make(),
    ];
  }

}
