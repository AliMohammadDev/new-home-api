<?php

namespace App\Filament\Resources\ProductImportItemResource\Pages;

use App\Filament\Resources\ProductImportItemResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProductImportItem extends EditRecord
{
  protected static string $resource = ProductImportItemResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url($this->getResource()::getUrl('index')),
    ];
  }

  protected function beforeFill(): void
  {
    if ($this->record->payments()->exists()) {
      Notification::make()
        ->title('وصول مرفوض')
        ->body('لا يمكن تعديل عمليات استيراد لها دفعات مسجلة.')
        ->danger()
        ->persistent()
        ->send();

      $this->redirect($this->getResource()::getUrl('index'));
    }
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
