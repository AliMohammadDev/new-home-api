<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ShippingWarehouseResource;
use App\Filament\Resources\WarehouseResource;
use App\Filament\Resources\WarehouseReturnResource;
use App\Models\Warehouse;
use App\Models\WarehouseReturn;
use App\Models\ShippingWarehouse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class WarehouseStatsWidget extends BaseWidget
{
  use HasWidgetShield;
  protected static ?int $sort = -7;
  protected ?string $heading = 'احصائيات المستودعات';

  protected function getStats(): array
  {
    return [
      Stat::make('عدد المستودعات نشطة', Warehouse::count())
        ->description('مواقع التخزين الحالية')
        ->descriptionIcon('heroicon-m-building-office-2')
        ->chart([15, 12, 18, 10, 15, 8, 12])

        ->color('primary')
        ->url(WarehouseResource::getUrl('index')),

      Stat::make('إجمالي المرتجعات', WarehouseReturn::query()->forActiveYear()->sum('amount'))
        ->description('قطع عادت للمخازن')
        ->descriptionIcon('heroicon-m-arrow-path')
        ->chart([10, 15, 12, 18, 14, 20, 18])

        ->color('danger')
        ->url(WarehouseReturnResource::getUrl('index')),

      Stat::make('شحنات قيد الوصول', ShippingWarehouse::query()->forActiveYear()->count())
        ->description('عمليات توزيع جارية')
        ->descriptionIcon('heroicon-m-truck')
        ->chart([15, 12, 18, 10, 15, 8, 12])

        ->color('warning')
        ->url(ShippingWarehouseResource::getUrl('index')),
    ];
  }
}
