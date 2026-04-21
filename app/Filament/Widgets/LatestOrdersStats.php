<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CompanyTreasureResource;
use App\Filament\Resources\OrderResource;
use App\Models\CompanyTreasure;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class LatestOrdersStats extends BaseWidget
{
  use HasWidgetShield;

  protected static ?int $sort = -10;
  protected static ?string $pollingInterval = '30s';

  public static function canView(): bool
  {
    return auth()->check() && auth()->user()->hasRole('super_admin');
  }


  protected function getStats(): array
  {
    $orderStats = Order::selectRaw("
        COUNT(*) as total_count,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count
    ")->first();

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


      Stat::make('الطلبات المكتملة', $orderStats->completed_count . ' طلب')
        ->description('تم تسليم ' . $orderStats->completed_count . ' طلب بنجاح')
        ->descriptionIcon('heroicon-m-arrow-trending-up')
        ->color('success')
        ->url(OrderResource::getUrl('index'))
        ->chart([5, 10, 8, 15, 12, 20, 25])
        ->extraAttributes([
          'class' => 'ring-2 ring-success-500/50',
        ]),

      Stat::make('طلبات بانتظار المعالجة', $orderStats->pending_count . ' طلب')
        ->description('هناك ' . $orderStats->pending_count . ' طلب يحتاج انتباهك')
        ->descriptionIcon('heroicon-m-pause-circle')
        ->color('warning')
        ->url(OrderResource::getUrl('index'))
        ->chart([15, 12, 18, 10, 15, 8, 12])
        ->extraAttributes([
          'class' => 'ring-2 ring-warning-500/50',
        ]),


    ];
  }

}