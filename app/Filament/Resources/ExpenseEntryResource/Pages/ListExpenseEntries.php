<?php

namespace App\Filament\Resources\ExpenseEntryResource\Pages;

use App\Filament\Resources\ExpenseEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpenseEntries extends ListRecords
{
    protected static string $resource = ExpenseEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
