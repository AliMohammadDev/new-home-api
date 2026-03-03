<?php

namespace App\Filament\Resources\CashierSalesFatoraResource\Pages;

use App\Filament\Resources\CashierSalesFatoraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashierSalesFatoras extends ListRecords
{
    protected static string $resource = CashierSalesFatoraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
