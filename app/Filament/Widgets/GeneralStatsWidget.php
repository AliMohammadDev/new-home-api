<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Category;
use App\Models\ProductVariant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class GeneralStatsWidget extends BaseWidget
{
  use HasWidgetShield;
  protected static ?int $sort = -9;

  protected function getStats(): array
  {
    return [
      Stat::make('المستخدمين المسجلين', User::count())
        ->description('نمو قاعدة العملاء')
        ->descriptionIcon('heroicon-m-users')
        ->color('info')
        ->chart([1, 3, 2, 5, 4, 7, 9]),

      Stat::make('الأصناف والنشاطات', Category::count())
        ->description('تنوع المنتجات في المتجر')
        ->descriptionIcon('heroicon-m-tag')
        ->color('gray')
        ->chart([2, 2, 5, 2, 2, 6, 4]),

      Stat::make('خيارات المنتجات (Variants)', ProductVariant::count())
        ->description('إجمالي الموديلات والقياسات')
        ->descriptionIcon('heroicon-m-rectangle-group')
        ->color('primary')
        ->chart([3, 7, 5, 8, 4, 9, 12]),
    ];
  }
}
