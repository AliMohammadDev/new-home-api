<?php

namespace App\Filament\Resources\SalesPointCashierResource\Pages;

use App\Filament\Resources\SalesPointCashierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalesPointCashiers extends ListRecords
{
  protected static string $resource = SalesPointCashierResource::class;

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