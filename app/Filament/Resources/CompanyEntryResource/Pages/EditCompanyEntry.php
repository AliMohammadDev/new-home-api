<?php

namespace App\Filament\Resources\CompanyEntryResource\Pages;

use App\Filament\Resources\CompanyEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyEntry extends EditRecord
{
    protected static string $resource = CompanyEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
