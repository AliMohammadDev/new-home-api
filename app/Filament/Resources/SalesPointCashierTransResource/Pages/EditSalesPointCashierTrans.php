<?php

namespace App\Filament\Resources\SalesPointCashierTransResource\Pages;

use App\Filament\Resources\SalesPointCashierTransResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesPointCashierTrans extends EditRecord
{
    protected static string $resource = SalesPointCashierTransResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
