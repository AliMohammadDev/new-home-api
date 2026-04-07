<?php

namespace App\Filament\Resources\CompanyEntryResource\Pages;

use App\Filament\Resources\CompanyEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyEntry extends CreateRecord
{
  protected static string $resource = CompanyEntryResource::class;

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}