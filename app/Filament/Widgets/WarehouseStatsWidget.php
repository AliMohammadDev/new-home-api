<?php

namespace App\Filament\Widgets;

use App\Models\Warehouse;
use App\Models\WarehouseReturn;
use App\Models\ShippingWarehouse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WarehouseStatsWidget extends BaseWidget
{
    protected static ?int $sort = -7;

    protected function getStats(): array
    {
        return [
            Stat::make('عدد المستودعات نشطة', Warehouse::count())
                ->description('مواقع التخزين الحالية')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('إجمالي المرتجعات', WarehouseReturn::sum('amount'))
                ->description('قطع عادت للمخازن')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('danger'),

            Stat::make('شحنات قيد الوصول', ShippingWarehouse::count())
                ->description('عمليات توزيع جارية')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),
        ];
    }
}
