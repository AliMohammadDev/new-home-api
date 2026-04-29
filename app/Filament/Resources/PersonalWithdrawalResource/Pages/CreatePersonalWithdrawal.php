<?php

namespace App\Filament\Resources\PersonalWithdrawalResource\Pages;

use App\Filament\Resources\PersonalWithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonalWithdrawal extends CreateRecord
{
  protected static string $resource = PersonalWithdrawalResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url($this->getResource()::getUrl('index')),
    ];
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}