<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class LatestOrdersStats extends BaseWidget
{
  use HasWidgetShield;

  protected static ?int $sort = -10;
  protected static ?string $pollingInterval = '30s';

  protected function getStats(): array
  {
    $stats = Order::selectRaw("
        COUNT(*) as total_count,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count
    ")->first();

    return [
      Stat::make('الطلبات المكتملة', $stats->completed_count . ' طلب')
        ->description('تم تسليم ' . $stats->completed_count . ' طلب بنجاح')
        ->descriptionIcon('heroicon-m-arrow-trending-up')
        ->chart([5, 10, 8, 15, 12, 20, 25])
        ->color('success')
        ->url(OrderResource::getUrl('index'))
        ->extraAttributes([
          'class' => 'ring-2 ring-success-500/50',
        ]),

      Stat::make('طلبات بانتظار المعالجة', $stats->pending_count . ' طلب')
        ->description('هناك ' . $stats->pending_count . ' طلب يحتاج انتباهك')
        ->descriptionIcon('heroicon-m-pause-circle')
        ->chart([15, 12, 18, 10, 15, 8, 12])
        ->color('warning')
        ->url(OrderResource::getUrl('index'))
        ->extraAttributes([
          'class' => 'ring-2 ring-warning-500/50',
        ]),
    ];
  }
}
