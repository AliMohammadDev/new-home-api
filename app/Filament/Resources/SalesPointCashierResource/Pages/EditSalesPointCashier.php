<?php

namespace App\Filament\Resources\SalesPointCashierResource\Pages;

use App\Filament\Resources\SalesPointCashierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesPointCashier extends EditRecord
{
  protected static string $resource = SalesPointCashierResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url($this->getResource()::getUrl('index')),
    ];
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}