<?php

namespace App\Filament\Resources\CompanyTreasureResource\Widgets;

use App\Models\CompanyTreasure;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CapitalStatsWidget extends BaseWidget
{

  protected static ?int $sort = -20;

  public static function canView(): bool
  {
    return auth()->check() && auth()->user()->hasRole('super_admin');
  }

  protected function getStats(): array
  {
    $totalCapital = CompanyTreasure::sum('money');

    return [
      Stat::make('إجمالي رأس المال', number_format($totalCapital, 2) . ' $')
        ->description('السيولة المتوفرة في  صندوق الشركة')
        ->descriptionIcon('heroicon-m-banknotes')
        ->color('success')
        ->chart([7, 3, 5, 4, 6, 2, 5, 9])
        ->extraAttributes([
          'class' => 'ring-2 ring-success-500/50',
        ]),
    ];
  }
}
