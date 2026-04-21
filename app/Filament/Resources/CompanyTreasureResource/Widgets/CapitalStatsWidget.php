<?php

namespace App\Filament\Resources\CompanyTreasureResource\Widgets;

use App\Filament\Resources\CompanyTreasureResource;
use App\Models\CompanyTreasure;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CapitalStatsWidget extends BaseWidget
{
  protected static ?int $sort = -20;

  protected int|string|array $columnSpan = 'full';

  public static function canView(): bool
  {
    return auth()->check() && auth()->user()->hasRole('super_admin');
  }

  protected function getStats(): array
  {
    $totalCapital = CompanyTreasure::sum('money');

    $onlineStoreTreasure = CompanyTreasure::where('name', 'صندوق مبيعات المتجر الالكتروني')->first();
    $onlineMoney = $onlineStoreTreasure ? $onlineStoreTreasure->money : 0;

    return [
      Stat::make('صندوق الشركة', number_format($totalCapital, 2) . ' $')
        ->descriptionIcon('heroicon-m-banknotes')
        ->color('success')
        ->url(CompanyTreasureResource::getUrl('index'))

        ->chart([7, 3, 5, 4, 6, 2, 5, 9])
        ->extraAttributes([
          'class' => 'ring-2 ring-success-500/50',
        ]),

      Stat::make('مبيعات المتجر الإلكتروني', number_format($onlineMoney, 2) . ' $')
        ->descriptionIcon('heroicon-m-shopping-cart')
        ->color('primary')
        ->url(CompanyTreasureResource::getUrl('index'))
        ->chart([2, 5, 4, 8, 6, 10, 12])
        ->extraAttributes([
          'class' => 'ring-2 ring-primary-500/50',
        ]),
    ];
  }
}