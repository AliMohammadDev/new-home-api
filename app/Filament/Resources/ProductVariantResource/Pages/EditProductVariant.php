<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProductVariant extends EditRecord
{
  protected static string $resource = ProductVariantResource::class;

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

  protected function afterSave(): void
  {
    $this->getRecord()->load('images');

    $this->fillForm();

    Notification::make()
      ->title('تم التحديث بنجاح')
      ->success()
      ->send();
  }
}