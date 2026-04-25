<?php

namespace App\Filament\Resources\CompanyTreasureResource\Pages;

use App\Filament\Resources\CompanyTreasureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyTreasure extends EditRecord
{
  protected static string $resource = CompanyTreasureResource::class;

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