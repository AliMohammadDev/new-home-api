<?php

namespace App\Filament\Resources\CompanyEntryResource\Pages;

use App\Filament\Resources\CompanyEntryResource;
use App\Models\CompanyEntry;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCompanyEntries extends ListRecords
{
  protected static string $resource = CompanyEntryResource::class;

  // protected function getHeaderActions(): array
  // {
  //   return [
  //     Actions\CreateAction::make(),
  //   ];
  // }

  public function getTabs(): array
  {
    return [
      'all' => Tab::make('جميع الحركات')
        ->badge(CompanyEntry::count())
        ->icon('heroicon-m-list-bullet'),

      'deposit' => Tab::make('الدائن (إيداع)')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('trans_type', 'deposit'))
        ->icon('heroicon-m-arrow-up-circle')
        ->badge(CompanyEntry::where('trans_type', 'deposit')->count())
        ->badgeColor('success'),

      'withdraw' => Tab::make('المدين (سحب)')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('trans_type', 'withdraw'))
        ->icon('heroicon-m-arrow-down-circle')
        ->badge(CompanyEntry::where('trans_type', 'withdraw')->count())
        ->badgeColor('danger'),
    ];
  }
}