<?php

namespace App\Filament\Resources\CompanyTreasureResource\Pages;

use App\Filament\Resources\CompanyTreasureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyTreasure extends CreateRecord
{
  protected static string $resource = CompanyTreasureResource::class;

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
