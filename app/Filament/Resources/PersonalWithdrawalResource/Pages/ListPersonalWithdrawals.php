<?php

namespace App\Filament\Resources\PersonalWithdrawalResource\Pages;

use App\Filament\Resources\PersonalWithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonalWithdrawals extends ListRecords
{
  protected static string $resource = PersonalWithdrawalResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url($this->getResource()::getUrl('index')),
      Actions\CreateAction::make(),
    ];
  }
}