<?php

namespace App\Filament\Resources\CashierSaleResource\Pages;

use App\Filament\Resources\CashierSaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashierSales extends ListRecords
{
    protected static string $resource = CashierSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
