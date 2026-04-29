<?php

namespace App\Filament\Resources\PersonalWithdrawalEntryResource\Pages;

use App\Filament\Resources\PersonalWithdrawalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonalWithdrawalEntry extends EditRecord
{
    protected static string $resource = PersonalWithdrawalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
