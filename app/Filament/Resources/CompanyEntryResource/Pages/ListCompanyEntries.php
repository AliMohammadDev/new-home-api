<?php

namespace App\Filament\Resources\CompanyEntryResource\Pages;

use App\Filament\Resources\CompanyEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyEntries extends ListRecords
{
    protected static string $resource = CompanyEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
