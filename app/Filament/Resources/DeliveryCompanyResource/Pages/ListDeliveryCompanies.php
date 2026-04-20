<?php

namespace App\Filament\Resources\DeliveryCompanyResource\Pages;

use App\Filament\Resources\DeliveryCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryCompanies extends ListRecords
{
    protected static string $resource = DeliveryCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
