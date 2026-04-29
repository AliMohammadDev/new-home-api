<?php

namespace App\Filament\Resources\CompanySalesTransferEntryResource\Pages;

use App\Filament\Resources\CompanySalesTransferEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanySalesTransferEntry extends EditRecord
{
    protected static string $resource = CompanySalesTransferEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
