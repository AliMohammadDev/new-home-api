<?php

namespace App\Filament\Widgets;

use App\Models\Warehouse;
use Filament\Widgets\ChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class WarehouseInventoryChart extends ChartWidget
{
  use HasWidgetShield;


  protected static bool $isLazy = true;

  protected static ?string $heading = 'توزيع كميات المنتجات في المستودعات';
  protected static ?int $sort = -6;
  protected int|string|array $columnSpan = 1;

  protected function getType(): string
  {
    return 'bar';
  }

  protected function getData(): array
  {
    $warehouses = Warehouse::withSum('productVariants as total_amount', 'shipping_warehouses.amount')
      ->get();

    return [
      'datasets' => [
        [
          'label' => 'إجمالي الكمية المتوفرة',
          'data' => $warehouses->pluck('total_amount')->toArray(),
          'backgroundColor' => '#025043',
        ],
      ],
      'labels' => $warehouses->pluck('name')->toArray(),
    ];
  }
}