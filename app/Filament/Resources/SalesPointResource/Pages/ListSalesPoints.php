<?php

namespace App\Filament\Resources\SalesPointResource\Pages;

use App\Filament\Resources\SalesPointResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalesPoints extends ListRecords
{
    protected static string $resource = SalesPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
