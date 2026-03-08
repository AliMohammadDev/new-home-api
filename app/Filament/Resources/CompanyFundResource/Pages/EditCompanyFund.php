<?php

namespace App\Filament\Resources\CompanyFundResource\Pages;

use App\Filament\Resources\CompanyFundResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyFund extends EditRecord
{
    protected static string $resource = CompanyFundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
