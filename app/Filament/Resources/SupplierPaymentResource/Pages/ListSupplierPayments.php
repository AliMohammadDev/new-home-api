<?php

namespace App\Filament\Resources\SupplierPaymentResource\Pages;

use App\Filament\Resources\SupplierPaymentResource;
use App\Models\SupplierPayment;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSupplierPayments extends ListRecords
{
  protected static string $resource = SupplierPaymentResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }

  public function getTabs(): array
  {
    return [
      'all' => Tab::make('جميع الحركات')
        ->badge(SupplierPayment::count())
        ->icon('heroicon-m-list-bullet'),

      'deposit' => Tab::make('الدائن (إيداع)')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('trans_type', 'deposit'))
        ->icon('heroicon-m-arrow-up-circle')
        ->badge(SupplierPayment::where('trans_type', 'deposit')->count())
        ->badgeColor('success'),

      'withdraw' => Tab::make('المدين (سحب)')
        ->modifyQueryUsing(fn(Builder $query) => $query->where('trans_type', 'withdraw'))
        ->icon('heroicon-m-arrow-down-circle')
        ->badge(SupplierPayment::where('trans_type', 'withdraw')->count())
        ->badgeColor('danger'),
    ];
  }
}
