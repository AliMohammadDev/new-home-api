<?php

namespace App\Filament\Resources\SalesPointManagerResource\Pages;

use App\Filament\Resources\SalesPointManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesPointManager extends EditRecord
{
    protected static string $resource = SalesPointManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
