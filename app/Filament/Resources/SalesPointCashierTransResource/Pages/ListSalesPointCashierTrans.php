<?php

namespace App\Filament\Resources\SalesPointCashierTransResource\Pages;

use App\Filament\Resources\SalesPointCashierTransResource;
use App\Models\SalesPointCashierTrans;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSalesPointCashierTrans extends ListRecords
{
  protected static string $resource = SalesPointCashierTransResource::class;

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

  public function getTabs(): array
  {
    return [
      'all' => Tab::make('الكل')
        ->icon('heroicon-m-list-bullet'),

      'deposit' => Tab::make('الدائن (إيداع)')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('trans_type', 'deposit'))
        ->icon('heroicon-m-arrow-trending-up')
        ->badge(SalesPointCashierTrans::where('trans_type', 'deposit')->count())
        ->badgeColor('success'),

      'withdraw' => Tab::make('المدين (سحب)')
        ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('trans_type', ['withdraw', 'withdraw']))
        ->icon('heroicon-m-arrow-trending-down')
        ->badge(SalesPointCashierTrans::whereIn('trans_type', ['withdraw', 'withdraw'])->count())
        ->badgeColor('danger'),
    ];
  }

}
