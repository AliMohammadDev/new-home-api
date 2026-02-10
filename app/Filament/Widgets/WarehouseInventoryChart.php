<?php

namespace App\Filament\Widgets;

use App\Models\Warehouse;
use Filament\Widgets\ChartWidget;

class WarehouseInventoryChart extends ChartWidget
{

  protected static ?string $heading = 'توزيع كميات المنتجات في المستودعات';
  protected static ?int $sort = -6;
  protected int|string|array $columnSpan = 1;

  protected function getType(): string
  {
    return 'bar';
  }

  protected function getData(): array
  {

    $warehouses = Warehouse::with(['productVariants'])->get()->map(function ($warehouse) {
      return [
        'name' => $warehouse->name,
        'total_amount' => $warehouse->productVariants->sum('pivot.amount'),
      ];
    });

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
