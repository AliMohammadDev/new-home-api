<?php

namespace App\Filament\Resources\SizeResource\Pages;

use App\Filament\Resources\SizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSizes extends ListRecords
{
  protected static string $resource = SizeResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('back')
        ->label('رجوع')
        ->color('gray')
        ->url('/admin'),
      Actions\CreateAction::make(),
    ];
  }
}