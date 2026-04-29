<?php

namespace App\Filament\Resources\PersonalWithdrawalResource\Pages;

use App\Filament\Resources\PersonalWithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonalWithdrawal extends EditRecord
{
  protected static string $resource = PersonalWithdrawalResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url($this->getResource()::getUrl('index')),
      Actions\DeleteAction::make(),
    ];
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
