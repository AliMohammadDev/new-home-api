<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoriesCountWidget extends BaseWidget
{

  protected function getStats(): array
  {
    return [
      Stat::make('الأصناف', Category::count()),
    ];
  }
}