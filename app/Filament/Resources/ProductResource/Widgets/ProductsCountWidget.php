<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class ProductsCountWidget extends ChartWidget
{
  protected static ?string $heading = 'عدد المنتجات';

  protected function getData(): array
  {
    $labels = [];
    $data = [];

    $period = CarbonPeriod::create(now()->subMonths(5), '1 month', now());
    foreach ($period as $month) {
      $labels[] = $month->format('M');
      $data[] = Product::whereYear('created_at', $month->year)
        ->whereMonth('created_at', $month->month)
        ->count();
    }
    return [
      'labels' => $labels,
      'datasets' => [
        [
          'label' => 'عدد المنتجات',
          'data' => $data,
          'backgroundColor' => '#025043',
        ],
      ],
    ];
  }

  protected function getType(): string
  {
    return 'bar';
  }
}
