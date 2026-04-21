<?php

namespace App\Filament\Exports;

use App\Models\WarehouseReturn;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class WarehouseReturnExporter extends Exporter
{
  protected static ?string $model = WarehouseReturn::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('id')->label('رقم تسلسلي'),
      ExportColumn::make('productVariant.product.name')->label('اسم المنتج'),
      ExportColumn::make('user.name')->label('اسم المستخدم'),
      ExportColumn::make('warehouse_id')->label('اسم المستودع'),
      ExportColumn::make('arrival_time')->label('الوقت الوصول'),
      ExportColumn::make('amount')->label('الكمية'),
      ExportColumn::make('unit_name')->label('اسم الوحدة'),
      ExportColumn::make('unit_capacity')->label('سعة الوحدة'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your warehouse return export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}
