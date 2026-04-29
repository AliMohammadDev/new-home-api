<?php

namespace App\Filament\Resources\PersonalWithdrawalEntryResource\Pages;

use App\Filament\Resources\PersonalWithdrawalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonalWithdrawalEntries extends ListRecords
{
    protected static string $resource = PersonalWithdrawalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
