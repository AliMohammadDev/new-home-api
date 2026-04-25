<?php

namespace App\Filament\Resources\SalesPointResource\Pages;

use App\Filament\Resources\SalesPointResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesPoint extends EditRecord
{
  protected static string $resource = SalesPointResource::class;

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
