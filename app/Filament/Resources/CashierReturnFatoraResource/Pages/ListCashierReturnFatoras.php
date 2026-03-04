<?php

namespace App\Filament\Resources\CashierReturnFatoraResource\Pages;

use App\Filament\Resources\CashierReturnFatoraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashierReturnFatoras extends ListRecords
{
    protected static string $resource = CashierReturnFatoraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
