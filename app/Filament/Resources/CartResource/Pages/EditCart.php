<?php

namespace App\Filament\Resources\CartResource\Pages;

use App\Filament\Resources\CartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCart extends EditRecord
{
  protected static string $resource = CartResource::class;

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
}