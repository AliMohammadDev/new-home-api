<?php

namespace App\Filament\Resources\CompanyTreasureResource\Pages;

use App\Filament\Resources\CompanyTreasureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyTreasures extends ListRecords
{
    protected static string $resource = CompanyTreasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
