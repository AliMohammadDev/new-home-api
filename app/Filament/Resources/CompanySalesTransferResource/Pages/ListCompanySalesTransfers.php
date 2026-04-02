<?php

namespace App\Filament\Resources\CompanySalesTransferResource\Pages;

use App\Filament\Resources\CompanySalesTransferResource;
use App\Models\CompanySalesTransfer;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCompanySalesTransfers extends ListRecords
{
  protected static string $resource = CompanySalesTransferResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }


  public function getTabs(): array
  {
    return [
      'all' => Tab::make('الكل')
        ->icon('heroicon-m-list-bullet'),

      'deposit' => Tab::make('الدائن (إيداع)')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('trans_type', 'deposit'))
        ->icon('heroicon-m-arrow-trending-up')
        ->badge(CompanySalesTransfer::where('trans_type', 'deposit')->count())
        ->badgeColor('success'),

      'withdraw' => Tab::make('المدين (سحب)')
        ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('trans_type', ['withdraw']))
        ->icon('heroicon-m-arrow-trending-down')
        ->badge(CompanySalesTransfer::whereIn('trans_type', ['withdraw', 'withdraw'])->count())
        ->badgeColor('danger'),
    ];
  }
}
