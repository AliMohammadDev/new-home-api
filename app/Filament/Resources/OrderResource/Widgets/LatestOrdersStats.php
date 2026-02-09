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
    $completedOrders = Order::where('status', 'completed');
    $completedCount = $completedOrders->count();
    $completedSum = $completedOrders->sum('total_amount');

    $pendingOrders = Order::where('status', 'pending');
    $pendingCount = $pendingOrders->count();
    $pendingSum = $pendingOrders->sum('total_amount');

    return [
      Stat::make('الطلبات المكتملة', $completedCount)
        ->value($completedCount . ' طلب')
        ->description('إجمالي المبالغ: ' . number_format($completedSum, 2) . '$')
        ->descriptionIcon('heroicon-m-check-badge')
        ->chart([10, 15, 8, 12, 20, 25, 30])
        ->color('success'),

      Stat::make('طلبات بانتظار التنفيذ', $pendingCount)
        ->value($pendingCount . ' طلب')
        ->description('مبالغ معلقة: ' . number_format($pendingSum, 2) . '$')
        ->descriptionIcon('heroicon-m-clock')
        ->chart([5, 10, 15, 10, 20, 15, 10])
        ->color('warning'),

      Stat::make('صافي الإيرادات المُحصلة', number_format($completedSum, 2) . '$')
        ->description('الأرباح الفعلية من الطلبات المكتملة')
        ->descriptionIcon('heroicon-m-banknotes')
        ->color('primary'),
    ];
  }
}