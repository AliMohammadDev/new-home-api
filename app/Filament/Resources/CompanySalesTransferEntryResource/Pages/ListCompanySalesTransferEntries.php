<?php

namespace App\Filament\Resources\CompanySalesTransferEntryResource\Pages;

use App\Filament\Resources\CompanySalesTransferEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanySalesTransferEntries extends ListRecords
{
    protected static string $resource = CompanySalesTransferEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
