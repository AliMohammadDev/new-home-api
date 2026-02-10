<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LatestOrdersStats extends BaseWidget
{
  protected static ?int $sort = -10;
  protected static ?string $pollingInterval = '30s';

  protected function getStats(): array
  {
    $stats = Order::selectRaw("
            COUNT(*) as total_count,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as completed_sum,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'pending' THEN total_amount ELSE 0 END) as pending_sum
        ")->first();

    return [
      Stat::make('الطلبات المكتملة', $stats->completed_count . ' طلب')
        ->value(number_format($stats->completed_sum, 0) . ' $')
        ->description('تم تسليم ' . $stats->completed_count . ' طلب بنجاح')
        ->descriptionIcon('heroicon-m-arrow-trending-up')
        ->chart([5, 10, 8, 15, 12, 20, 25])
        ->color('success')
        ->extraAttributes([
          'class' => 'ring-2 ring-success-500/50',
        ]),

      Stat::make('طلبات بانتظار المعالجة', $stats->pending_count . ' طلب')
        ->value(number_format($stats->pending_sum, 0) . ' $')
        ->description('هناك ' . $stats->pending_count . ' طلب يحتاج انتباهك')
        ->descriptionIcon('heroicon-m-pause-circle')
        ->chart([15, 12, 18, 10, 15, 8, 12])
        ->color('warning')
        ->extraAttributes([
          'class' => 'ring-2 ring-warning-500/50',
        ]),

      Stat::make('معدل التحصيل', number_format($stats->completed_sum, 0) . ' $')
        ->description('إجمالي السيولة النقدية الحالية')
        ->descriptionIcon('heroicon-m-currency-dollar')
        ->color('primary')
        ->extraAttributes([
          'class' => 'cursor-pointer',
          'wire:click' => '$dispatch("status-filter", { status: "completed" })',
        ]),
    ];
  }
}
