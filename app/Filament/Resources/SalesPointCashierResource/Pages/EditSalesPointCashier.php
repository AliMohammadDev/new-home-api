<?php

namespace App\Filament\Resources\SalesPointCashierResource\Pages;

use App\Filament\Resources\SalesPointCashierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesPointCashier extends EditRecord
{
    protected static string $resource = SalesPointCashierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
